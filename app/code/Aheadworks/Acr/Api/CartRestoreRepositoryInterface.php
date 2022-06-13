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

use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CartRestoreRepositoryInterface
 * @package Aheadworks\Acr\Api
 */
interface CartRestoreRepositoryInterface
{
    /**
     * Save cart restore
     *
     * @param \Aheadworks\Acr\Api\Data\CartRestoreInterface $cartRestore
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function save(CartRestoreInterface $cartRestore);

    /**
     * Get cart restore
     *
     * @param int $cartRestoreId
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function get($cartRestoreId);

    /**
     * Get cart restore by code
     *
     * @param string $cartRestoreCode
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function getByCode($cartRestoreCode);

    /**
     * Get cart restore by event history id
     *
     * @param string $eventHistoryId
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function getByEventHistoryId($eventHistoryId);

    /**
     * Retrieve cart restores matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete cart restore
     *
     * @param \Aheadworks\Acr\Api\Data\CartRestoreInterface $cartRestore
     * @return bool
     */
    public function delete(CartRestoreInterface $cartRestore);

    /**
     * Delete cart restore by id
     *
     * @param int $cartRestoreId
     * @return bool
     */
    public function deleteById($cartRestoreId);
}
