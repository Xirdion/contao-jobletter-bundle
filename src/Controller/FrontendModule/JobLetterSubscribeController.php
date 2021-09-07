<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\Controller\FrontendModule;

use Contao\Controller;
use Contao\Environment;
use Contao\Idna;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Dreibein\JobletterBundle\Model\JobLetterDenyListModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobLetterSubscribeController extends AbstractJobLetterController
{
    /**
     * @return string
     */
    public function createFormId(): string
    {
        return 'tl_job_subscribe_' . $this->model->id;
    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return 'jl_confirm';
    }

    /**
     * @return string
     */
    public function getSubmitText(): string
    {
        return StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['subscribe']);
    }

    /**
     * @param string $email
     * @param array  $archives
     * @param array  $categories
     *
     * @return bool
     */
    public function validateAction(string $email, array $archives, array $categories): bool
    {
        // Get active subscriptions with different settings
        $subscriptionList = $this->getSubscriptionsWithDifferentCategories($email, $archives, $categories);

        // Check if there would be any new subscription from the selected archives
        $archives = array_diff($archives, $subscriptionList);
        if (true === empty($archives)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['subscribed'];

            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @param array  $archives
     * @param array  $categories
     *
     * @throws \Exception
     */
    public function submitAction(string $email, array $archives, array $categories): void
    {
        // Check if there are old inactive subscriptions and remove them
        $oldSubscriptions = JobLetterRecipientModel::findOldSubscriptionByEmailAndArchives($email, $archives);
        if (null !== $oldSubscriptions) {
            foreach ($oldSubscriptions as $oldSubscription) {
                $oldSubscription->delete();
            }
        }

        // Check for each archive if a subscription must be created or just updated
        // Check if a deny-list-entry must be deleted, created or updated
        $time = time();
        $relatedEntries = [];
        $hash = md5($email);
        foreach ($archives as $archive) {
            $removeDenyList = true;
            $subscription = JobLetterRecipientModel::findByEmailAndArchive($email, $archive);
            if (null !== $subscription) {
                // Check if there have been categories that now are not needed anymore
                $denyCats = array_diff($subscription->getCategories(), $categories);
                if (false === empty($denyCats)) {
                    // Deny-list-entry is needed
                    $removeDenyList = false;

                    // Create a deny-list entry with these categories
                    $denyEntry = JobLetterDenyListModel::findByHashAndArchive($hash, $archive);
                    if (null === $denyEntry) {
                        $denyEntry = new JobLetterDenyListModel();
                        $denyEntry->archive = $archive;
                        $denyEntry->hash = $hash;
                    }
                    $denyEntry->categories = $denyCats;
                    $denyEntry->save();
                }
            } else {
                // Create a new subscription entry
                $subscription = new JobLetterRecipientModel();
                $subscription->email = $email;
                $subscription->pid = $archive;
                $subscription->active = false;
                $subscription->addedOn = $time;
            }
            $subscription->tstamp = $time;
            $subscription->categories = $categories;
            $subscription->save();

            // Only remove deny-list-entry if the current one is not needed anymore
            if (true === $removeDenyList) {
                $denyEntry = JobLetterDenyListModel::findByHashAndArchive(md5($email), $archive);
                if (null !== $denyEntry) {
                    $denyEntry->delete();
                }
            }

            $relatedEntries['tl_job_letter_recipient'][] = $subscription->getId();
        }

        // Create the opt-in-token
        $optInToken = $this->optIn->create('jl', $email, $relatedEntries);

        // Set the simple token data
        $simpleTokens = [
            'archives' => $this->createArchiveTokenString($archives),
            'categories' => $this->createCategoryTokenString($categories),
            'token' => $optInToken->getIdentifier(),
            'domain' => Idna::decode(Environment::get('host')),
            'link' => Idna::decode(Environment::get('base')) . Environment::get('request') . ((false !== strpos(Environment::get('request'), '?')) ? '&' : '?') . 'token=' . $optInToken->getIdentifier(),
            'email' => $email,
        ];

        // Send the opt-in-message via mail
        $optInToken->send(
            sprintf($GLOBALS['TL_LANG']['MSC']['jl_subject'], Idna::decode(Environment::get('host'))),
            $this->parser->parse($this->model->jl_subscribe, $simpleTokens) // parse the text field from module-config
        );

        // Check if there is a redirect page
        if ($this->model->jumpTo) {
            /** @var PageModel $page */
            $page = $this->model->getRelated('jumpTo');
            if (null !== $page) {
                Controller::redirect($page->getFrontendUrl());
            }
        }

        // Add flash message and reload the page
        $this->session->getFlashBag()->set($this->getSessionKey(), $GLOBALS['TL_LANG']['MSC']['jl_confirm']);

        Controller::reload();
    }

    /**
     * @param Template    $template
     * @param ModuleModel $model
     * @param Request     $request
     *
     * @throws \Exception
     *
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        // Activate e-mail address
        if (0 === strncmp((string) $request->query->get('token'), 'jl-', 3)) {
            $this->activateRecipient($template);

            return $template->getResponse();
        }

        return parent::getResponse($template, $model, $request);
    }

    /**
     * Activate the subscriptions for the recipients by token.
     *
     * @param Template $template
     */
    protected function activateRecipient(Template $template): void
    {
        $optInToken = $this->optIn->find(Input::get('token'));
        if (null === $optInToken) {
            $template->mclass = 'error';
            $template->message = $GLOBALS['TL_LANG']['MSC']['invalidToken'];

            return;
        }

        if (false === $optInToken->isValid()) {
            $template->mclass = 'error';
            $template->message = $GLOBALS['TL_LANG']['MSC']['invalidToken'];

            return;
        }

        $relatedRecords = $optInToken->getRelatedRecords();
        $ids = current($relatedRecords);
        if ($relatedRecords < 1 || 'tl_job_letter_recipient' !== key($relatedRecords) || \count($ids) < 1) {
            $template->mclass = 'error';
            $template->message = $GLOBALS['TL_LANG']['MSC']['invalidToken'];

            return;
        }

        if (true === $optInToken->isConfirmed()) {
            $template->mclass = 'error';
            $template->message = $GLOBALS['TL_LANG']['MSC']['tokenConfirmed'];

            return;
        }

        $recipientList = [];

        // Validate the token
        foreach ($ids as $id) {
            $recipient = JobLetterRecipientModel::findById((int) $id);
            if (null === $recipient) {
                $template->mclass = 'error';
                $template->message = $GLOBALS['TL_LANG']['MSC']['invalidToken'];

                return;
            }

            if ($optInToken->getEmail() !== $recipient->getEmail()) {
                $template->mclass = 'error';
                $template->message = $GLOBALS['TL_LANG']['MSC']['tokenEmailMismatch'];

                return;
            }

            $recipientList[] = $recipient;
        }

        // Activate the subscriptions
        $time = time();
        foreach ($recipientList as $recipient) {
            $recipient->tstamp = $time;
            $recipient->active = true;
            $recipient->save();
        }

        $optInToken->confirm();

        $template->mclass = 'confirm';
        $template->message = $GLOBALS['TL_LANG']['MSC']['jl_activate'];
    }
}
