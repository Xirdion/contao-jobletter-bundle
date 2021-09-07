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

$GLOBALS['TL_DCA'][$table]['list']['global_operations']['send_all'] = [
    'icon' => 'bundles/dreibeinjobletter/send.svg',
];

$GLOBALS['TL_DCA'][$table]['list']['operations']['send'] = [
    'icon' => 'bundles/dreibeinjobletter/send.svg',
];
