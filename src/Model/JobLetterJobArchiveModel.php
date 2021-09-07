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

use Dreibein\JobpostingBundle\Model\JobArchiveModel;

/**
 * @property string      $mail_subject
 * @property string|null $mail_text
 * @property string      $mail_transport
 * @property string      $mail_sender
 * @property string      $mail_senderName
 */
class JobLetterJobArchiveModel extends JobArchiveModel
{
    /**
     * @return string
     */
    public function getMailSubject(): string
    {
        return $this->mail_subject;
    }

    /**
     * @return string
     */
    public function getMailText(): string
    {
        return (string) $this->mail_text;
    }

    /**
     * @return string
     */
    public function getMailTransport(): string
    {
        return $this->mail_transport;
    }

    /**
     * @return string
     */
    public function getMailSender(): string
    {
        return $this->mail_sender;
    }

    /**
     * @return string
     */
    public function getMailSenderName(): string
    {
        return $this->mail_senderName;
    }
}
