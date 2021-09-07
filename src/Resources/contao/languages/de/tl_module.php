<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$table = 'tl_module';

$GLOBALS['TL_LANG'][$table]['jl_archives'] = ['Job-Archive', 'Bitte wählen Sie ein oder mehr Job-Archive aus.'];
$GLOBALS['TL_LANG'][$table]['jl_hideArchives'] = ['Job-Archive-Menü ausblenden', 'Das Menü zum Auswählen der Archive im FE nicht anzeigen.'];
$GLOBALS['TL_LANG'][$table]['jl_categories'] = ['Job-Kategorien', 'Bitte wählen Sie eine oder mehr Job-Kategorien aus.'];
$GLOBALS['TL_LANG'][$table]['jl_hideCategories'] = ['Job-Kategorie-Menü ausblenden', 'Das Menü zum Auswählen der Kategorien im FE nicht anzeigen.'];
$GLOBALS['TL_LANG'][$table]['jl_template'] = ['Jobletter-Template', 'Hier können Sie das Jobletter-Template auswählen'];
$GLOBALS['TL_LANG'][$table]['jl_subscribe'] = ['Abonnementbestätigung', 'Sie können die Platzhalter <em>##archives##</em> (Name der Archive), <em>##categories##</em> (Name der Kategorien), <em>##domain##</em> (Domainname), <em>##link##</em> (Aktivierungslink) und <em>##email##</em> (E-Mail-Adresse) verwenden.'];
$GLOBALS['TL_LANG'][$table]['jl_unsubscribe'] = ['Abonnementbestätigung', 'Sie können die Platzhalter <em>##archives##</em> (Name der Archive), <em>##categories##</em> (Name der Kategorien), <em>##domain##</em> (Domainname) und <em>##email##</em> (E-Mail-Adresse) verwenden.'];
$GLOBALS['TL_LANG'][$table]['jl_text'] = ['Eigener Text', 'Hier können Sie z.B. einen Datenschutzhinweis eingeben, um die Anmeldung DSGVO-konform zu gestalten.'];
