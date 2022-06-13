<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Acr
 * @version    1.1.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Acr\Cron;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class MailSender
 * @package Aheadworks\Acr\Cron
 */
class MailSender extends CronAbstract
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 270;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param DateTime $dateTime
     * @param Config $config
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        DateTime $dateTime,
        Config $config,
        QueueManagementInterface $queueManagement
    ) {
        $this->config = $config;
        $this->queueManagement = $queueManagement;
        parent::__construct($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->config->isEnabled()
            || $this->isLocked($this->config->getSendEmailsLastExecTime(), self::RUN_INTERVAL)
        ) {
            return $this;
        }

        $this->queueManagement->sendScheduledEmails();

        $this->config->setSendEmailsLastExecTime($this->getCurrentTime());
        return $this;
    }
}
