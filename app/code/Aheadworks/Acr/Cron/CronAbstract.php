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

use Magento\Framework\Stdlib\DateTime\DateTime;

abstract class CronAbstract
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * Main cron job entry point
     *
     * @return $this
     */
    abstract public function execute();

    /**
     * Is cron job locked
     *
     * @param int $lastExecTime
     * @param int $interval
     * @return bool
     */
    protected function isLocked($lastExecTime, $interval)
    {
        $now = $this->getCurrentTime();
        return $now < $lastExecTime + $interval;
    }

    /**
     * Get current time
     *
     * @return int
     */
    protected function getCurrentTime()
    {
        $now = $this->dateTime->timestamp();
        return $now;
    }
}
