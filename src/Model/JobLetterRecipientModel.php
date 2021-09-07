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
use Dreibein\JobletterBundle\Repository\JobLetterRecipientRepository;

/**
 * @property int    $tstamp
 * @property int    $pid
 * @property array  $categories
 * @property string $email
 * @property bool   $active
 * @property int    $addedOn
 */
class JobLetterRecipientModel extends JobLetterRecipientRepository
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return (int) $this->pid;
    }

    /**
     * @return int
     */
    public function getTstamp(): int
    {
        return (int) $this->tstamp;
    }

    /**
     * @return int
     */
    public function getArchive(): int
    {
        return (int) $this->pid;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    /**
     * @return int
     */
    public function getAddedOn(): int
    {
        return (int) $this->addedOn;
    }
}
