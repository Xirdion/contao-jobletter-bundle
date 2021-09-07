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

use Contao\CoreBundle\Mailer\AvailableTransports;
use Contao\CoreBundle\ServiceAnnotation\Callback;

class JobArchiveListener
{
    private AvailableTransports $transports;

    /**
     * @param AvailableTransports $transports
     */
    public function __construct(AvailableTransports $transports)
    {
        $this->transports = $transports;
    }

    /**
     * @Callback(table="tl_job_archive", target="fields.mail_transport.options")
     *
     * @return array
     */
    public function getMailTransportOptions(): array
    {
        return $this->transports->getTransportOptions();
    }
}
