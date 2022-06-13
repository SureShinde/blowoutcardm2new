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
 * Interface CartRestoreSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface CartRestoreSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cart restore list
     *
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface[]
     */
    public function getItems();

    /**
     * Set cart restore list
     *
     * @param \Aheadworks\Acr\Api\Data\CartRestoreInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
