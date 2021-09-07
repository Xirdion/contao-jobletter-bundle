<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$table = 'tl_job_archive';

// legends
$GLOBALS['TL_LANG'][$table]['mail_legend'] = 'E-Mail-Einstellungen';
$GLOBALS['TL_LANG'][$table]['sender_legend'] = 'Absendereinstellungen';

// fields
$GLOBALS['TL_LANG'][$table]['mail_subject'] = ['Betreff', 'Bitte geben Sie den Betreff der E-Mail an.'];
$GLOBALS['TL_LANG'][$table]['mail_text'] = ['Inhalt', 'Hier können Sie den Inhalt der E-Mail eingeben. Sie können die Platzhalter <em>##email##</em> (E-Mail-Adresse des Empfängers), <em>##archive##</em> (Name des Archivs), <em>##categories##</em> (Namen der Kategorien), <em>##job##</em> (Titel des Jobs) und <em>##job_link##</em> (Link zum Jobs) verwenden.'];
$GLOBALS['TL_LANG'][$table]['mail_transport'] = ['Mailer-Transport', 'Hier können Sie den Mailer-Transport überschreiben.'];
$GLOBALS['TL_LANG'][$table]['mail_sender'] = ['Individuelle Absender-E-Mail-Adresse', 'Hier können Sie die Standard-E-Mail-Adresse des Absenders überschreiben.'];
$GLOBALS['TL_LANG'][$table]['mail_senderName'] = ['Individueller Absendername', 'Hier können Sie den Standard-Absendernamen überschreiben.'];

// buttons
$GLOBALS['TL_LANG'][$table]['recipients'] = 'Abonnenten des Archivs ID %s bearbeiten';
