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
namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;

/**
 * Interface RuleManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface RuleManagementInterface
{
    /**
     * Validate and return valid rules
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return \Aheadworks\Acr\Api\Data\RuleSearchResultsInterface
     */
    public function validate(CartHistoryInterface $cartHistory);

    /**
     * Get email send time
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @param string $triggerTime
     * @return string
     */
    public function getEmailSendTime(RuleInterface $rule, $triggerTime);

    /**
     * Get email preview
     *
     * @param int $storeId
     * @param string $subject
     * @param string $content
     * @return \Aheadworks\Acr\Model\Preview
     */
    public function getPreview($storeId, $subject, $content);
}
