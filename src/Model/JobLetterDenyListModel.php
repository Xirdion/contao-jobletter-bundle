<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\Model;

use Contao\StringUtil;
use Dreibein\JobletterBundle\Repository\JobLetterDenyListRepository;

/**
 * @property int    $archive
 * @property array  $categories
 * @property string $hash
 */
class JobLetterDenyListModel extends JobLetterDenyListRepository
{
    /**
     * @return int
     */
    public function getArchive(): int
    {
        return (int) $this->archive;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return StringUtil::deserialize($this->categories, true);
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
