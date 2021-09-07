<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\Resources\contao\config;

use Dreibein\JobletterBundle\Model\JobLetterDenyListModel;
use Dreibein\JobletterBundle\Model\JobLetterJobArchiveModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientSentModel;

$GLOBALS['BE_MOD']['content']['jobs']['tables'][] = 'tl_job_letter_recipient';

$GLOBALS['TL_MODELS']['tl_job_letter_recipient'] = JobLetterRecipientModel::class;
$GLOBALS['TL_MODELS']['tl_job_letter_recipient_sent'] = JobLetterRecipientSentModel::class;
$GLOBALS['TL_MODELS']['tl_job_letter_deny_list'] = JobLetterDenyListModel::class;
$GLOBALS['TL_MODELS']['tl_job_archive'] = JobLetterJobArchiveModel::class;
