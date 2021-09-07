<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job letter bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobletterBundle\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Image;
use Contao\StringUtil;
use Dreibein\JobletterBundle\Job\JobLetter;
use Dreibein\JobletterBundle\Model\JobLetterRecipientSentModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class JobListener
{
    private JobLetter $jobLetter;
    private ContaoFramework $framework;
    private Request $request;

    /**
     * @param JobLetter       $jobLetter
     * @param ContaoFramework $framework
     */
    public function __construct(JobLetter $jobLetter, ContaoFramework $framework, RequestStack $requestStack)
    {
        $this->jobLetter = $jobLetter;
        $this->framework = $framework;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Callback(table="tl_job", target="list.global_operations.send_all.button")
     *
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string      $class
     * @param string      $attributes
     *
     * @return string
     */
    public function handleGlobalSendAction(?string $href, string $label, string $title, string $class, string $attributes): string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        if ('all' === $this->request->query->get('send_id')) {
            $archiveId = (int) $this->request->query->get('id');
            $this->jobLetter->sendMessagesForAllJobs($archiveId);
            $controller->redirect(Controller::getReferer());
        }

        // Add the custom parameter to the url
        $href .= '&amp;send_id=all';

        return '<a href="' . Controller::addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '" class="' . $class . '"' . $attributes . '>' . $label . '</a> ';
    }

    /**
     * @Callback(table="tl_job", target="list.operations.send.button")
     *
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function handleSendAction(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        if ($this->request->query->get('send_id')) {
            $this->jobLetter->sendMessages((int) $this->request->query->get('send_id'));
            $controller->redirect(Controller::getReferer());
        }

        // Load the job-model
        $job = JobModel::findById((int) $record['id']);
        if (null === $job) {
            return '';
        }

        // Check if the job is active
        if (false === $job->isActive()) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
        }

        // Check if there are any recipients that have not been informed yet
        // or the last message was within the last 30 days for this job
        $hasRecipients = JobLetterRecipientSentModel::hasUninformedRecipients($job);
        if (false === $hasRecipients) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
        }

        // Add the custom parameter to the url
        $href .= '&amp;send_id=' . $record['id'];

        return '<a href="' . $controller->addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
    }
}
