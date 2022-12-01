<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\Job;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\InternalServerErrorException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\CoreBundle\String\SimpleTokenParser;
use Contao\Email;
use Contao\Idna;
use Contao\Message;
use Contao\PageModel;
use Dreibein\JobletterBundle\Model\JobLetterJobArchiveModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientModel;
use Dreibein\JobletterBundle\Model\JobLetterRecipientSentModel;
use Dreibein\JobpostingBundle\Job\UrlGenerator;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class JobLetter
{
    private LoggerInterface $logger;
    private ContaoFramework $framework;
    private SimpleTokenParser $parser;
    private UrlGenerator $urlGenerator;
    private JobLetterJobArchiveModel $jobArchive;
    private JobModel $job;
    private Adapter $categoryModel;
    private Adapter $recipientSentModel;

    /**
     * @param ContaoFramework   $framework
     * @param SimpleTokenParser $parser
     * @param UrlGenerator      $urlGenerator
     */
    public function __construct(LoggerInterface $logger, ContaoFramework $framework, SimpleTokenParser $parser, UrlGenerator $urlGenerator)
    {
        $this->logger = $logger;
        $this->framework = $framework;
        $this->parser = $parser;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Try to send an email to all recipients of all the active jobs.
     *
     * @param int $archiveId
     */
    public function sendMessagesForAllJobs(int $archiveId): void
    {
        $jobModel = $this->framework->getAdapter(JobModel::class);
        $jobs = $jobModel->findPublishedByPids([$archiveId]);
        if (null === $jobs) {
            return;
        }

        foreach ($jobs as $job) {
            $this->sendMessages($job->getId());
        }
    }

    /**
     * Try to send a message for a given job.
     *
     * @param int $jobId
     */
    public function sendMessages(int $jobId): void
    {
        // Adapt some contao classes
        $jobModel = $this->framework->getAdapter(JobModel::class);
        $archiveModel = $this->framework->getAdapter(JobLetterJobArchiveModel::class);
        $config = $this->framework->getAdapter(Config::class);
        $controller = $this->framework->getAdapter(Controller::class);
        $this->categoryModel = $this->framework->getAdapter(JobCategoryModel::class);
        $this->recipientSentModel = $this->framework->getAdapter(JobLetterRecipientSentModel::class);

        // Find the job model by the given ID
        $job = $jobModel->findById($jobId);
        if (null === $job) {
            return;
        }
        $this->job = $job;

        // Find the archive of the job
        $archive = $archiveModel->findById($this->job->getPid());
        if (null === $archive) {
            return;
        }
        $this->jobArchive = $archive;

        // Set the mail sender address
        $this->jobArchive->mail_sender = $this->jobArchive->getMailSender() ?: $config->get('adminEmail');
        if (!$this->jobArchive->mail_sender) {
            throw new InternalServerErrorException('No sender address given. Please check the job archive settings.');
        }

        // replace inserttags in subject and text of the email
        $this->jobArchive->mail_subject = $controller->replaceInsertTags($this->jobArchive->getMailSubject(), false);
        $this->jobArchive->mail_text = $controller->replaceInsertTags($this->jobArchive->getMailText(), false);

        // Get all recipients that must get informed
        $recipients = $this->recipientSentModel->findUninformedRecipients($job);
        if (null === $recipients) {
            return;
        }

        // Prepare the email for sending
        $email = $this->generateEmail();
        $rejected = [];
        $time = time();
        $total = 0;

        // Loop over all available recipients and try to send an email
        foreach ($recipients as $recipient) {
            $this->sendEmail($email, $recipient, $time, $rejected, $total);
        }

        // If there are rejected recipients
        if (false === empty($rejected)) {
            $recipientModel = $this->framework->getAdapter(JobLetterRecipientModel::class);
            $recipients = $recipientModel->findActiveByEmails($rejected);
            if (null !== $recipients) {
                foreach ($recipients as $recipient) {
                    $recipient->active = false;
                    $recipient->save();

                    $msg = sprintf('Recipient address "%s" was rejected and has been deactivated', Idna::decodeEmail($recipient->getEmail()));
                    $this->logger->error($msg, ['contao' => new ContaoContext(__METHOD__, 'ERROR')]);
                }
            }
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_job']['send_rejected'], \count($rejected)));
        }

        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_job']['send_confirm'], $total));
    }

    /**
     * Create an e-mail object with sender data pre-filled.
     *
     * @return Email
     */
    private function generateEmail(): Email
    {
        $email = new Email();
        $email->from = $this->jobArchive->getMailSender();
        $email->fromName = $this->jobArchive->getMailSenderName();
        $email->subject = $this->jobArchive->getMailSubject();
        $email->logFile = 'JOBLETTER_' . $this->job->getId();

        // Set a specific transport protocol
        if ('' !== $this->jobArchive->getMailTransport()) {
            $email->addHeader('X-Transport', $this->jobArchive->getMailTransport());
        }

        $email->addHeader('List-Unsubscribe', '<mailto:' . $this->jobArchive->getMailSender() . '?subject=' . rawurlencode($GLOBALS['TL_LANG']['MSC']['unsubscribe']) . '>');

        return $email;
    }

    /**
     * @param Email                   $email
     * @param JobLetterRecipientModel $recipient
     * @param int                     $time
     * @param array                   $rejected
     * @param int                     $total
     */
    private function sendEmail(Email $email, JobLetterRecipientModel $recipient, int $time, array &$rejected, int &$total): void
    {
        $tokens = $this->getEmailTokens($recipient);
        $email->text = $this->parser->parse($this->jobArchive->getMailText(), $tokens);

        try {
            $email->sendTo($recipient->getEmail());
        } catch (RfcComplianceException $e) {
            $rejected[] = $recipient->getEmail();

            return;
        }

        if ($email->hasFailures()) {
            $rejected[] = $recipient->getEmail();

            return;
        }

        // Update the sent status
        ++$total;
        $this->updateRecipientSent($this->job->getId(), $recipient->getId(), $time);
    }

    /**
     * Create the array of simple tokens for the email text.
     *
     * @param JobLetterRecipientModel $recipient
     *
     * @return array
     */
    private function getEmailTokens(JobLetterRecipientModel $recipient): array
    {
        // Generate the category-text
        $categoryText = '';
        $categories = $this->categoryModel->findByIds($recipient->getCategories());
        if (null !== $categories) {
            foreach ($categories as $category) {
                if ('' !== $categoryText) {
                    $categoryText .= ', ';
                }
                $categoryText .= $category->getFrontendTitle();
            }
        }

        // Get the unsubscribe-url
        $unsubscribeUrl = '';
        $pageModel = $this->framework->getAdapter(PageModel::class);
        $page = $pageModel->findById((int) $this->jobArchive->mail_unsubscribe_link);
        if (null !== $page) {
            $unsubscribeUrl = $page->getAbsoluteUrl();
        }

        return [
            'email' => $recipient->getEmail(),
            'archive' => $this->jobArchive->getFrontendTitle(),
            'categories' => $categoryText,
            'job' => $this->job->getFrontendTitle(),
            'job_link' => $this->urlGenerator->generateJobUrl($this->job, true),
            'unsubscribe_link' => $unsubscribeUrl,
        ];
    }

    /**
     * Create or update the job-recipient-sent table.
     *
     * @param int $jobId
     * @param int $recipientId
     * @param int $time
     */
    private function updateRecipientSent(int $jobId, int $recipientId, int $time): void
    {
        $sentModel = $this->recipientSentModel->findByJobAndRecipient($jobId, $recipientId);
        if (null === $sentModel) {
            $sentModel = new JobLetterRecipientSentModel();
            $sentModel->job = $jobId;
            $sentModel->recipient = $recipientId;
        }
        $sentModel->sent = $time;
        $sentModel->save();
    }
}
