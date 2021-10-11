<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

use Contao\Config;

$table = 'tl_job_archive';

$GLOBALS['TL_DCA'][$table]['config']['ctable'][] = 'tl_job_letter_recipient';

$GLOBALS['TL_DCA'][$table]['list']['operations']['recipients'] = [
    'href' => 'table=tl_job_letter_recipient',
    'icon' => 'mgroup.svg',
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_subject'] = [
    'exclude' => true,
    'search' => true,
    'sorting' => true,
    'flag' => 1,
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 128, 'notnull' => true, 'default' => ''],
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_text'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['mandatory' => true, 'decodeEntities' => true, 'class' => 'noresize', 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_unsubscribe_link'] = [
    'exclude' => true,
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio'],
    'sql' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'notnull' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_transport'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_sender'] = [
    'exclude' => true,
    'search' => true,
    'filter' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'email', 'maxlength' => 255, 'decodeEntities' => true, 'placeholder' => Config::get('adminEmail'), 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
];

$GLOBALS['TL_DCA'][$table]['fields']['mail_senderName'] = [
    'exclude' => true,
    'search' => true,
    'sorting' => true,
    'flag' => 11,
    'inputType' => 'text',
    'eval' => ['decodeEntities' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 128, 'notnull' => true, 'default' => ''],
];
