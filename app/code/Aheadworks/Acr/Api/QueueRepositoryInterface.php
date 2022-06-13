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

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Queue CRUD interface
 * @api
 */
interface QueueRepositoryInterface
{
    /**
     * Save queue
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return \Aheadworks\Acr\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function save(QueueInterface $queue);

    /**
     * Retrieve queue
     *
     * @param int $queueId
     * @return \Aheadworks\Acr\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function get($queueId);

    /**
     * Retrieve queues matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete queue
     * Related cart history item will be deleted if there are no more queue items with this cart history id
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function delete(QueueInterface $queue);

    /**
     * Delete queue by id
     * Related cart history item will be deleted if there are no more queue items with this cart history id
     *
     * @param int $queueId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function deleteById($queueId);

    /**
     * Delete queue by cart history id
     * Related cart history item will NOT be deleted!!!
     *
     * @param int $cartHistoryId
     * @return bool true on success
     */
    public function deleteByCartHistoryId($cartHistoryId);
}
