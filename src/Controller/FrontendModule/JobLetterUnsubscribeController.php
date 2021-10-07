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
use Contao\Email;
use Contao\Idna;
use Contao\PageModel;
use Contao\StringUtil;
use Dreibein\JobletterBundle\Model\JobLetterDenyListModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;

class JobLetterUnsubscribeController extends AbstractJobLetterController
{
    /**
     * @return string
     */
    public function createFormId(): string
    {
        return 'tl_job_unsubscribe_' . $this->model->id;
    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return 'jl_removed';
    }

    /**
     * @return string
     */
    public function getSubmitText(): string
    {
        return StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['unsubscribe']);
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

        // Check if there is an active subscription of the selected archives
        $archives = array_intersect($archives, $subscriptionList);
        if (true === empty($archives)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['unsubscribed'];

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
        // Loop over the active subscriptions and check if some must be updated or deleted
        $subscriptions = JobLetterRecipientModel::findByEmailAndArchives($email, $archives);
        if (null !== $subscriptions) {
            foreach ($subscriptions as $subscription) {
                // Create hash for deny list
                $hash = md5($subscription->getEmail());

                // Store archive ID for deny list
                $archiveId = $subscription->getArchive();

                // Check if any categories would stay active for the current subscription
                $currentCats = $subscription->getCategories();
                $remainingCats = array_diff($currentCats, $categories);
                if (true === empty($remainingCats)) {
                    // subscription can be deleted
                    $subscription->delete();
                } else {
                    // subscription must be updated
                    $subscription->tstamp = time();
                    $subscription->categories = $remainingCats;
                    $subscription->save();
                }

                // Update or create deny list entry
                $denyEntry = JobLetterDenyListModel::findByHashAndArchive($hash, $archiveId);
                if (null === $denyEntry) {
                    $denyEntry = new JobLetterDenyListModel();
                    $denyEntry->archive = $archiveId;
                    $denyEntry->hash = $hash;
                }
                $denyEntry->categories = $categories;
                $denyEntry->save();
            }
        }

        $simpleTokens = [
            'archives' => $this->createArchiveTokenString($archives),
            'categories' => $this->createCategoryTokenString($categories),
            'domain' => Idna::decode($this->request->getSchemeAndHttpHost()),
            'email' => $email,
        ];

        $senderAddress = $GLOBALS['TL_ADMIN_EMAIL'];
        $page = $this->getPageModel();
        if (null !== $page) {
            // Try to use the admin email address from the root page
            $page->loadDetails();
            if ($page->adminEmail) {
                $senderAddress = $page->adminEmail;
            }
        }

        $confirmationMail = new Email();
        $confirmationMail->from = $senderAddress;
        $confirmationMail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $confirmationMail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['jl_subject'], Idna::decode($this->request->getSchemeAndHttpHost()));
        $confirmationMail->text = $this->parser->parse($this->model->jl_unsubscribe, $simpleTokens);
        $confirmationMail->sendTo($email);

        // Check if there is a redirect page
        if ($this->model->jumpTo) {
            /** @var PageModel $page */
            $page = $this->model->getRelated('jumpTo');
            if (null !== $page) {
                Controller::redirect($page->getFrontendUrl());
            }
        }

        // Add flash message and reload the page
        $this->session->getFlashBag()->set($this->getSessionKey(), $GLOBALS['TL_LANG']['MSC']['jl_removed']);

        Controller::reload();
    }
}
