<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Image;
use Contao\StringUtil;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class JobLetterRecipientListener
{
    private ContaoFramework $framework;
    private Request $request;

    /**
     * @param ContaoFramework $framework
     * @param RequestStack    $requestStack
     */
    public function __construct(ContaoFramework $framework, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Get a list of available job categories.
     *
     * @Callback(table="tl_job_letter_recipient", target="fields.categories.options")
     *
     * @return array
     */
    public function getJobCategories(): array
    {
        $categories = JobCategoryModel::findAll();
        if (null === $categories) {
            return [];
        }

        $list = [];
        /** @var JobCategoryModel $category */
        foreach ($categories as $category) {
            $list[$category->getId()] = $category->getTitle();
        }

        return $list;
    }

    /**
     * @Callback(table="tl_job_letter_recipient", target="list.operations.toggle.button")
     *
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function toggleIcon(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        if ($this->request->query->get('tid')) {
            $this->toggleActive((int) $this->request->query->get('tid'), (1 === (int) $this->request->query->get('state')));
            $controller->redirect(Controller::getReferer());
        }

        $href .= '&amp;tid=' . $record['id'] . '&amp;state=' . ($record['active'] ? '' : 1);

        if (!$record['active']) {
            $icon = 'invisible.svg';
        }

        return '<a href="' . $controller->addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label, 'data-state="' . ($record['active'] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * @param int  $id
     * @param bool $active
     */
    private function toggleActive(int $id, bool $active): void
    {
        $recipientModel = $this->framework->getAdapter(JobLetterRecipientModel::class);
        $recipient = $recipientModel->findById($id);
        if (null === $recipient) {
            return;
        }

        $recipient->active = $active;
        $recipient->save();
    }
}
