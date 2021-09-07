<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\Repository;

use Contao\Model;
use Dreibein\JobletterBundle\Model\JobLetterDenyListModel;

abstract class JobLetterDenyListRepository extends Model
{
    protected static $strTable = 'tl_job_letter_deny_list';

    /**
     * @param int $id
     *
     * @return JobLetterDenyListModel|null
     */
    public static function findById(int $id): ?JobLetterDenyListModel
    {
        return static::findByPk($id);
    }

    /**
     * @param string $hash
     * @param int    $archive
     *
     * @return JobLetterDenyListModel|null
     */
    public static function findByHashAndArchive(string $hash, int $archive): ?JobLetterDenyListModel
    {
        $table = static::$strTable;

        $columns = [
            $table . '.hash = ?',
            $table . '.archive = ?',
        ];

        $values = [
            $hash,
            $archive,
        ];

        return static::findOneBy($columns, $values);
    }
}
