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

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\OptIn\OptInInterface;
use Contao\CoreBundle\Util\SimpleTokenParser;
use Contao\FormCaptcha;
use Contao\FrontendTemplate;
use Contao\Idna;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\Validator;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractJobLetterController extends AbstractFrontendModuleController implements JobLetterInterface
{
    protected OptInInterface $optIn;
    protected SimpleTokenParser $parser;
    protected Session $session;
    protected Template $template;
    protected ModuleModel $model;
    protected Request $request;
    protected string $formId;

    /**
     * @param OptInInterface    $optIn
     * @param SimpleTokenParser $parser
     * @param Session           $session
     */
    public function __construct(OptInInterface $optIn, SimpleTokenParser $parser, Session $session)
    {
        $this->optIn = $optIn;
        $this->parser = $parser;
        $this->session = $session;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $this->template = $template;
        $this->model = $model;
        $this->request = $request;

        $this->formId = $this->createFormId();

        // Depending on the settings of the module model another template is used.
        if ($model->jl_template) {
            $template = new FrontendTemplate($model->jl_template);
            $template->setData($model->arrData);

            $this->template = $template;
        }

        // Set some initial values for the template
        $this->initTemplateData();

        // Create the security captcha widget
        $captcha = $this->createCaptchaWidget();

        // Validate the form
        if ($this->formId === Input::post('FORM_SUBMIT')) {
            $submitted = $this->validateForm($captcha);
            if (false !== $submitted) {
                $this->submitAction(...$submitted);
            }
        }

        $this->template->captcha = $captcha->parse();

        // Add a confirmation message to the template
        if ($this->session->isStarted()) {
            $flashBag = $this->session->getFlashBag();
            if ($flashBag->has($this->getSessionKey())) {
                $messages = $flashBag->get($this->getSessionKey());

                $this->template->mclass = 'confirm';
                $this->template->message = $messages[0];
            }
        }

        $archiveList = [];
        $archives = JobArchiveModel::findByIds(StringUtil::deserialize($this->model->jl_archives, true));
        if (null !== $archives) {
            foreach ($archives as $archive) {
                $archiveList[$archive->getId()] = $archive->getTitle();
            }
        }
        $this->template->archives = $archiveList;

        $categoryList = [];
        $categories = JobCategoryModel::findByIds(StringUtil::deserialize($this->model->jl_categories, true));
        if (null !== $categories) {
            foreach ($categories as $category) {
                $categoryList[$category->getId()] = $category->getTitle();
            }
        }
        $this->template->categories = $categoryList;

        return $this->template->getResponse();
    }

    /**
     * Set the initial values to the template.
     */
    protected function initTemplateData(): void
    {
        $this->template->showForm = true;
        $this->template->email = '';
        $this->template->archives = [];
        $this->template->showArchives = !$this->model->jl_hideArchives;
        $this->template->selectedArchives = [];
        $this->template->categories = [];
        $this->template->showCategories = !$this->model->jl_hideCategories;
        $this->template->selectedCategories = [];
        $this->template->submit = $this->getSubmitText();
        $this->template->archivesLabel = $GLOBALS['TL_LANG']['MSC']['jl_archives'];
        $this->template->categoriesLabel = $GLOBALS['TL_LANG']['MSC']['jl_categories'];
        $this->template->emailLabel = $GLOBALS['TL_LANG']['MSC']['emailAddress'];
        $this->template->formId = $this->formId;
        $this->template->id = $this->model->id;
    }

    /**
     * Create the needed security captcha widget.
     *
     * @return FormCaptcha
     */
    protected function createCaptchaWidget(): FormCaptcha
    {
        $captchaField = [
            'name' => 'subscribe_' . $this->model->id,
            'label' => $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
            'inputType' => 'captcha',
            'eval' => ['mandatory' => true],
        ];

        return new FormCaptcha(FormCaptcha::getAttributesFromDca($captchaField, $captchaField['name']));
    }

    /**
     * Validate the given html form.
     *
     * @param FormCaptcha $captcha
     *
     * @return array|false
     */
    protected function validateForm(FormCaptcha $captcha)
    {
        // Check the given mail address
        $email = Idna::encodeEmail(Input::post('email', true));
        if (false === Validator::isEmail($email)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['email'];

            return false;
        }

        $this->template->email = $email;

        // Validate the archive selection
        $archives = Input::post('archives');
        if (false === \is_array($archives)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['noArchives'];

            return false;
        }

        // Just select the archives that are possible from the given configuration
        $archives = array_intersect($archives, StringUtil::deserialize($this->model->jl_archives, true));
        if (true === empty($archives) || false === \is_array($archives)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['noArchives'];

            return false;
        }

        // Cast all the archive IDs into integer values
        $archives = array_map(static function ($id) {
            return (int) $id;
        }, $archives);

        $this->template->selectedArchives = $archives;

        // Validate the category selection
        $categories = (array) Input::post('categories');
        $categories = array_intersect($categories, StringUtil::deserialize($this->model->jl_categories, true));
        if (false === \is_array($categories)) {
            $this->template->mclass = 'error';
            $this->template->message = $GLOBALS['TL_LANG']['ERR']['noCategories'];

            return false;
        }

        // Cast all the category IDs into integer values
        $categories = array_map(static function ($id) {
            return (int) $id;
        }, $categories);

        $this->template->selectedCategories = $categories;

        // Run custom validation method
        $check = $this->validateAction($email, $archives, $categories);
        if (false === $check) {
            return false;
        }

        $captcha->validate();
        if (true === $captcha->hasErrors()) {
            return false;
        }

        return [$email, $archives, $categories];
    }

    /**
     * Loop over active subscriptions and check if there are any different selections from the submitted data.
     *
     * @param string $email
     * @param array  $archives
     * @param array  $categories
     *
     * @return array
     */
    protected function getSubscriptionsWithDifferentCategories(string $email, array $archives, array $categories): array
    {
        // Find currently active subscriptions
        $subscriptionList = [];
        $subscriptions = JobLetterRecipientModel::findActiveByEmail($email);
        if (null !== $subscriptions) {
            foreach ($subscriptions as $subscription) {
                // Check if there is already an active subscription for this archive
                if (\in_array($subscription->getArchive(), $archives, true)) {
                    // Check if there are different categories as in the active record
                    $catDiff = array_diff($categories, $subscription->getCategories());
                    if (true === empty($catDiff)) {
                        // Add those entries that already active subscription with the same categories
                        $subscriptionList[] = $subscription->getArchive();
                    }
                }
            }
        }

        return $subscriptionList;
    }

    /**
     * @param array $archives
     *
     * @return string
     */
    protected function createArchiveTokenString(array $archives): string
    {
        $archiveModels = JobArchiveModel::findByIds($archives);
        if (null === $archiveModels) {
            return '';
        }

        return implode(', ', $archiveModels->fetchEach('title'));
    }

    /**
     * @param array $categories
     *
     * @return string
     */
    protected function createCategoryTokenString(array $categories): string
    {
        if (true === empty($categories)) {
            return '';
        }

        $categoryModels = JobCategoryModel::findByIds($categories);
        if (null === $categoryModels) {
            return '';
        }

        return implode(', ', $categoryModels->fetchEach('title'));
    }
}
