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

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * CartHistory CRUD interface
 * @api
 */
interface CartHistoryRepositoryInterface
{
    /**
     * Save cart history
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return \Aheadworks\Acr\Api\Data\CartHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function save(CartHistoryInterface $cartHistory);

    /**
     * Retrieve cart history
     *
     * @param int $cartHistoryId
     * @return \Aheadworks\Acr\Api\Data\CartHistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function get($cartHistoryId);

    /**
     * Retrieve cart histories matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete cart history
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function delete(CartHistoryInterface $cartHistory);

    /**
     * Delete cart history by id
     *
     * @param int $cartHistoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function deleteById($cartHistoryId);
}
