<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$table = 'tl_job';

// buttons
$GLOBALS['TL_LANG'][$table]['send_all'] = ['alle Job-Nachrichten versenden', 'alle Job-Nachrichten versenden'];
$GLOBALS['TL_LANG'][$table]['send'] = ['Job-Nachricht versenden', 'Job-Nachricht für den Job %s versenden'];

$GLOBALS['TL_LANG'][$table]['send_confirm'] = 'Eine E-Mail wurde an %s Empfänger versendet.';
$GLOBALS['TL_LANG'][$table]['send_rejected'] = '%s ungültige E-Mail-Adresse(n) wurde(n) deaktiviert (siehe System-Log).';
