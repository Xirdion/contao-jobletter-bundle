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

use Dreibein\JobletterBundle\Repository\JobLetterRecipientSentRepository;

/**
 * @property int $job
 * @property int $recipient
 * @property int $sent
 */
class JobLetterRecipientSentModel extends JobLetterRecipientSentRepository
{
    /**
     * @return int
     */
    public function getJob(): int
    {
        return (int) $this->job;
    }

    /**
     * @return int
     */
    public function getRecipient(): int
    {
        return (int) $this->recipient;
    }

    /**
     * @return int
     */
    public function getSent(): int
    {
        return (int) $this->sent;
    }
}
