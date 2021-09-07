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
use Dreibein\JobletterBundle\Model\JobLetterRecipientSentModel;
use Dreibein\JobpostingBundle\Model\JobModel;

abstract class JobLetterRecipientSentRepository extends Model
{
    protected static $strTable = 'tl_job_letter_recipient_sent';

    /**
     * @param int $id
     *
     * @return JobLetterRecipientSentModel|null
     */
    public static function findById(int $id): ?JobLetterRecipientSentModel
    {
        return static::findByPk($id);
    }

    /**
     * Find an entry for a job ID and a recipient ID.
     *
     * @param int $jobId
     * @param int $recipientId
     *
     * @return JobLetterRecipientSentModel|null
     */
    public static function findByJobAndRecipient(int $jobId, int $recipientId): ?JobLetterRecipientSentModel
    {
        $table = static::$strTable;

        $columns = [
            $table . '.job = ?',
            $table . '.recipient = ?',
        ];

        $values = [
            $jobId,
            $recipientId,
        ];

        return static::findOneBy($columns, $values);
    }

    /**
     * Check if there are recipients and those recipients have not been informed by now.
     * If there are already informed recipients, check if the last mail was sent before 30 days.
     *
     * @param JobModel $job
     *
     * @return bool
     */
    public static function hasUninformedRecipients(JobModel $job): bool
    {
        // Check if there are any recipients for the job
        $recipientList = static::getRecipientList($job);
        $informedList = static::findInformedRecipientIds($job->getId(), $recipientList);

        // Check if all recipients have been informed
        return \count($informedList) !== \count($recipientList);
    }

    /**
     * @param JobModel $job
     *
     * @return JobLetterRecipientModel[]|Model\Collection|null
     */
    public static function findUninformedRecipients(JobModel $job): ?Model\Collection
    {
        // Check if there are any recipients for the job
        $recipientList = static::getRecipientList($job);
        $informedList = static::findInformedRecipientIds($job->getId(), $recipientList);

        $uninformedIds = array_diff($recipientList, $informedList);
        if (empty($uninformedIds)) {
            return null;
        }

        return JobLetterRecipientModel::findByIds($uninformedIds);
    }

    /**
     * Get all IDs of recipients that have already been informed.
     *
     * @param int   $jobId
     * @param array $recipients
     *
     * @return array
     */
    protected static function findInformedRecipientIds(int $jobId, array $recipients): array
    {
        if (empty($recipients)) {
            return [];
        }

        // Check if the recipients has already been informed within the last 30 days
        $table = static::$strTable;

        $date = new \DateTimeImmutable();
        $date = $date->sub(new \DateInterval('P30D'));

        $columns = [
            $table . '.job = ?',
            $table . '.recipient IN (?)',
            $table . '.sent >= ?',
        ];

        $values = [
            $jobId,
            implode(',', $recipients),
            $date->getTimestamp(),
        ];

        $entries = static::findBy($columns, $values);
        if (null === $entries) {
            return [];
        }

        return $entries->fetchEach('recipient');
    }

    /**
     * @param JobModel $job
     *
     * @return array
     */
    protected static function getRecipientList(JobModel $job): array
    {
        $recipients = JobLetterRecipientModel::getActiveByArchive($job->getPid());
        if (null === $recipients) {
            return [];
        }

        // Compare the categories of the job and the recipients
        $recipientList = [];
        $categories = $job->getCategories();
        foreach ($recipients as $recipient) {
            // Recipient has no special categories
            if (empty($recipient->getCategories())) {
                // Add the recipient to the list
                $recipientList[] = $recipient->getId();
                continue;
            }

            // Check if there are categories within the job that the recipient has subscribed
            $intersection = array_intersect($categories, $recipient->getCategories());
            if (empty($intersection)) {
                continue;
            }

            // Add the recipient to the list
            $recipientList[] = $recipient->getId();
        }

        return $recipientList;
    }
}
