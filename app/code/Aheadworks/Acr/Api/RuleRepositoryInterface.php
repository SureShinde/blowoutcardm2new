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
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Rule CRUD interface
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * Save rule
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @return \Aheadworks\Acr\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function save(RuleInterface $rule);

    /**
     * Retrieve rule
     *
     * @param int $ruleId
     * @return \Aheadworks\Acr\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If rule does not exist
     */
    public function get($ruleId);

    /**
     * Retrieve rules matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If rule does not exist
     */
    public function delete(RuleInterface $rule);

    /**
     * Delete rule by id
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If rule does not exist
     */
    public function deleteById($ruleId);
}
