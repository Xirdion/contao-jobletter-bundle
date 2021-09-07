<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$GLOBALS['TL_LANG']['tl_job_letter_recipient'] = [
    // legend
    'email_legend' => 'E-Mail-Einstellung',
    'job_legend' => 'Job-Einstellung',

    // fields
    'email' => ['E-Mail-Adresse', 'Bitte geben Sie die E-Mail-Adresse des Abonnenten ein.'],
    'active' => ['Abonnenten aktivieren', 'Abonnenten werden normalerweise automatisch aktiviert (double-opt-in).'],
    'addedOn' => ['Registrierungsdatum', 'Das Datum des Abonnements.'],
    'pid' => ['Job-Archiv', 'Ein Abonnement bezieht sich immer auf ein Job-Archiv.'],
    'categories' => ['Job-Kategorien', 'Pro Abonnement können unterschiedliche Job-Kategorien verwendet werden.'],

    // buttons
    'new' => ['Neu', 'Ein neues Abonnement anlegen'],
    'all' => ['Mehrere bearbeiten', 'Mehrere Abonnements auf einmal bearbeiten'],
    'edit' => ['bearbeiten', 'Abonnement %s bearbeiten'],
    'copy' => ['duplizieren', 'Abonnement duplizieren'],
    'delete' => ['löschen', 'Abonnement %s löschen'],
    'show' => ['Details anzeigen', 'Details des Abonnements %s anzeigen'],
];
