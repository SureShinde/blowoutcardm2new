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
 * Interface QueueSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface QueueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get queue list
     *
     * @return \Aheadworks\Acr\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * Set queue list
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
