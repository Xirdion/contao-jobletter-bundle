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

interface JobLetterInterface
{
    /**
     * Create an unique form ID for the html form.
     *
     * @return string
     */
    public function createFormId(): string;

    /**
     * Get the session flash-bag identifier key.
     *
     * @return string
     */
    public function getSessionKey(): string;

    /**
     * Get the text for the submit button of the form.
     *
     * @return string
     */
    public function getSubmitText(): string;

    /**
     * Run a custom method to validate the form data.
     *
     * @param string $email
     * @param array  $archives
     * @param array  $categories
     *
     * @return bool
     */
    public function validateAction(string $email, array $archives, array $categories): bool;

    /**
     * Run a custom method if the form was validated successfully.
     *
     * @param string $email
     * @param array  $archives
     * @param array  $categories
     */
    public function submitAction(string $email, array $archives, array $categories): void;
}
