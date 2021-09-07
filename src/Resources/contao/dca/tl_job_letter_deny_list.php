<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$GLOBALS['TL_DCA']['tl_job_letter_deny_list'] = [
    'config' => [
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'archive,hash' => 'unique',
            ],
        ],
    ],

    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true, 'notnull' => true],
        ],
        'archive' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'categories' => [
            'sql' => ['type' => 'blob', 'notnull' => false],
        ],
        'hash' => [
            'sql' => ['type' => 'string', 'length' => 32, 'default' => '', 'notnull' => true],
        ],
    ],
];
