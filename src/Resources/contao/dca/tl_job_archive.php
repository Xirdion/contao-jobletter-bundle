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
use Contao\CoreBundle\DataContainer\PaletteManipulator;

$table = 'tl_job_archive';

$GLOBALS['TL_DCA'][$table]['config']['ctable'][] = 'tl_job_letter_recipient';

$GLOBALS['TL_DCA'][$table]['list']['operations']['recipients'] = [
    'href' => 'table=tl_job_letter_recipient',
    'icon' => 'mgroup.svg',
];

PaletteManipulator::create()
    ->addLegend('mail_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['mail_subject', 'mail_text'], 'mail_legend', PaletteManipulator::POSITION_APPEND)
    ->addLegend('sender_legend', 'mail_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['mail_transport', 'mail_sender', 'mail_senderName'], 'sender_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', $table)
;

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
