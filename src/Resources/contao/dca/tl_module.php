<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

use Contao\Controller;

$table = 'tl_module';

$subscribePalette = <<<'PALETTE'
{title_legend},name,headline,type;
{config_legend},jl_archives,jl_hideArchives,jl_categories,jl_hideCategories;
{text_legend},jl_text;
{redirect_legend},jumpTo;
{email_legend},jl_subscribe;
{template_legend:hide},jl_template;
{protected_legend:hide},protected;
{expert_legend:hide},guests,cssID;
PALETTE;

$unsubscribePalette = <<<'PALETTE'
{title_legend},name,headline,type;
{config_legend},jl_archives,jl_hideArchives,jl_categories,jl_hideCategories;
{redirect_legend},jumpTo;
{email_legend},jl_unsubscribe;
{template_legend:hide},jl_template;
{protected_legend:hide},protected;
{expert_legend:hide},guests,cssID;
PALETTE;

$GLOBALS['TL_DCA'][$table]['palettes']['job_letter_subscribe'] = $subscribePalette;
$GLOBALS['TL_DCA'][$table]['palettes']['job_letter_unsubscribe'] = $unsubscribePalette;

$GLOBALS['TL_DCA'][$table]['fields']['jl_archives'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['jl_hideArchives'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => ['type' => 'boolean', 'notnull' => true, 'default' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['jl_categories'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['jl_hideCategories'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => ['type' => 'boolean', 'notnull' => true, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jl_text'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['jl_template'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        return Controller::getTemplateGroup('jl_');
    },
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'notnull' => true, 'default' => ''],
];

// Text for opt-in message
$GLOBALS['TL_DCA'][$table]['fields']['jl_subscribe'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['style' => 'height:120px', 'decodeEntities' => true, 'alwaysSave' => true],
    'sql' => ['type' => 'text', 'notnull' => false],
];

// Text for opt-in message
$GLOBALS['TL_DCA'][$table]['fields']['jl_unsubscribe'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['style' => 'height:120px', 'decodeEntities' => true, 'alwaysSave' => true],
    'sql' => ['type' => 'text', 'notnull' => false],
];
