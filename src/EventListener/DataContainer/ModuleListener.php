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

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleListener
{
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_module", target="fields.jl_archives.options")
     */
    public function loadJobArchives(): array
    {
        $archives = JobArchiveModel::findAll();
        if (null === $archives) {
            return [];
        }

        $archiveList = [];
        foreach ($archives as $archive) {
            $archiveList[$archive->getId()] = $archive->getTitle();
        }

        return $archiveList;
    }

    /**
     * @Callback(table="tl_module", target="fields.jl_categories.options")
     */
    public function loadJobCategories(): array
    {
        $categories = JobCategoryModel::findAll();
        if (null === $categories) {
            return [];
        }

        $categoryList = [];
        foreach ($categories as $category) {
            $categoryList[$category->getId()] = $category->getTitle();
        }

        return $categoryList;
    }

    /**
     * @Callback(table="tl_module", target="fields.jl_subscribe.load")
     *
     * @param string|null $text
     *
     * @return string
     */
    public function loadDefaultSubscribeText(?string $text): string
    {
        if ('' !== trim((string) $text)) {
            return $text;
        }

        $lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';

        return $this->translator->trans('tl_module.jl_text_subscribe.1', [], 'DreibeinJobletterBundle', $lang);
    }

    /**
     * @Callback(table="tl_module", target="fields.jl_unsubscribe.load")
     *
     * @param string|null $text
     *
     * @return string
     */
    public function loadDefaultUnsubscribeText(?string $text): string
    {
        if ('' !== trim((string) $text)) {
            return $text;
        }

        $lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';

        return $this->translator->trans('tl_module.jl_text_unsubscribe.1', [], 'DreibeinJobletterBundle', $lang);
    }
}
