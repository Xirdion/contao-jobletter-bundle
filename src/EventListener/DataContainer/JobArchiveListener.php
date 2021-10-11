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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\Mailer\AvailableTransports;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;

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
     * @Callback(table="tl_job_archive", target="config.onload")
     *
     * @param DataContainer $dataContainer
     */
    public function modifyPalette(DataContainer $dataContainer): void
    {
        PaletteManipulator::create()
            ->addLegend('mail_legend', 'apply_legend', PaletteManipulator::POSITION_AFTER)
            ->addField(['mail_subject', 'mail_text', 'mail_unsubscribe_link'], 'mail_legend', PaletteManipulator::POSITION_APPEND)
            ->addLegend('sender_legend', 'mail_legend', PaletteManipulator::POSITION_AFTER)
            ->addField(['mail_transport', 'mail_sender', 'mail_senderName'], 'sender_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', $dataContainer->table)
        ;
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
