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

/**
 * Interface CartRestoreManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface CartRestoreManagementInterface
{
    /**
     * Save restore code
     *
     * @param int $cartHistoryId
     * @param int $quoteId
     */
    public function saveRestoreCode($eventHistoryId, $quoteId);

    /**
     * Get cart restore item by history id
     *
     * @param int $eventHistoryId
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function getCartRestoreItemByHistoryId($eventHistoryId);
}
