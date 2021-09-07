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
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;

abstract class JobLetterRecipientRepository extends Model
{
    protected static $strTable = 'tl_job_letter_recipient';

    /**
     * Find a specific recipient model by its ID.
     *
     * @param int $id
     *
     * @return JobLetterRecipientModel|null
     */
    public static function findById(int $id): ?JobLetterRecipientModel
    {
        return static::findByPk($id);
    }

    /**
     * @param string $email
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findActiveByEmail(string $email): ?Model\Collection
    {
        $table = static::$strTable;

        $columns = [
            $table . '.email = ?',
            $table . '.active = ?',
        ];

        $values = [
            $email,
            true,
        ];

        return static::findBy($columns, $values);
    }

    /**
     * @param array $emails
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findActiveByEmails(array $emails): ?Model\Collection
    {
        $table = static::$strTable;

        $columns = [
            $table . '.email IN(?)',
            $table . '.active = ?',
        ];

        $values = [
            implode(',', $emails),
            true,
        ];

        return static::findBy($columns, $values);
    }

    /**
     * @param string $email
     * @param int    $archive
     *
     * @return JobLetterRecipientModel|null
     */
    public static function findByEmailAndArchive(string $email, int $archive): ?JobLetterRecipientModel
    {
        $table = static::$strTable;

        $columns = [
            $table . '.email = ?',
            $table . '.pid = ?',
        ];

        $values = [
            $email,
            $archive,
        ];

        return static::findOneBy($columns, $values);
    }

    /**
     * @param string $email
     * @param array  $archives
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findByEmailAndArchives(string $email, array $archives)
    {
        if (true === empty($archives)) {
            return null;
        }

        $table = static::$strTable;

        $columns = [
            $table . '.email = ?',
            $table . '.pid IN (?)',
        ];

        $values = [
            $email,
            implode(',', $archives),
        ];

        return static::findBy($columns, $values);
    }

    /**
     * Find old (inactive) subscriptions for an mail address and an array of archives.
     *
     * @param string $email
     * @param array  $archives
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findOldSubscriptionByEmailAndArchives(string $email, array $archives): ?Model\Collection
    {
        if (true === empty($archives)) {
            return null;
        }

        $table = static::$strTable;

        $columns = [
            $table . '.email = ?',
            $table . '.pid IN (?)',
            $table . '.active = ?',
        ];

        $values = [
            $email,
            implode(',', $archives),
            false,
        ];

        return static::findBy($columns, $values);
    }

    /**
     * Find all active entries of a given archive.
     * Categories must be checked within PHP.
     *
     * @param int $archive
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function getActiveByArchive(int $archive): ?Model\Collection
    {
        if (0 === $archive) {
            return null;
        }

        $table = static::$strTable;

        $columns = [
            $table . '.pid = ?',
            $table . '.active = ?',
        ];

        $values = [
            $archive,
            true,
        ];

        return static::findBy($columns, $values);
    }

    /**
     * @param array $ids
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findByIds(array $ids): ?Model\Collection
    {
        if (true === empty($ids)) {
            return null;
        }

        $table = static::$strTable;

        $columns = [
            $table . '.id IN (?)',
            $table . '.active = ?',
        ];

        $values = [
            implode(',', $ids),
            true,
        ];

        return static::findBy($columns, $values);
    }
}
