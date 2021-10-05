<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\OptIn;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\OptIn\OptInToken as ContaoOptInToken;
use Contao\CoreBundle\OptIn\OptInTokenAlreadyConfirmedException;
use Contao\CoreBundle\OptIn\OptInTokenNoLongerValidException;
use Contao\Email;
use Contao\OptInModel;

class OptInToken extends ContaoOptInToken
{
    private OptInModel $model;
    private ContaoFramework $framework;
    private string $senderAddress = '';
    private string $senderName = '';

    /**
     * @param OptInModel      $model
     * @param ContaoFramework $framework
     */
    public function __construct(OptInModel $model, ContaoFramework $framework)
    {
        parent::__construct($model, $framework);
        $this->model = $model;
        $this->framework = $framework;
    }

    public function setSender(string $address, ?string $name = null): self
    {
        $this->senderAddress = $address;
        if (null !== $name) {
            $this->senderName = $name;
        }

        return $this;
    }

    public function send(string $subject = null, string $text = null): void
    {
        if ($this->isConfirmed()) {
            throw new OptInTokenAlreadyConfirmedException();
        }

        if (!$this->isValid()) {
            throw new OptInTokenNoLongerValidException();
        }

        if (!$this->hasBeenSent()) {
            if (null === $subject || null === $text) {
                throw new \LogicException('Please provide subject and text to send the token');
            }

            $this->model->emailSubject = $subject;
            $this->model->emailText = $text;
            $this->model->save();
        }

        /** @var Email $email */
        $email = $this->framework->createInstance(Email::class);
        $email->subject = $this->model->emailSubject;
        $email->text = $this->model->emailText;

        if ('' !== $this->senderAddress) {
            $email->from = $this->senderAddress;
        }

        if ('' !== $this->senderName) {
            $email->fromName = $this->senderName;
        }

        $email->sendTo($this->model->email);
    }
}
