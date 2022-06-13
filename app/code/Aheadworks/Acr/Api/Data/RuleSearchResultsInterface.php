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
namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RuleSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rules list
     *
     * @return \Aheadworks\Acr\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set rules list
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
