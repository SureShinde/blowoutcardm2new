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
namespace Aheadworks\Acr\Ui\Component\Listing\Columns\Rule;

use Magento\Store\Ui\Component\Listing\Column\Store as StoreColumn;

/**
 * Class Store
 * @package Aheadworks\Acr\Ui\Component\Listing\Columns\Rule
 * @codeCoverageIgnore
 */
class Store extends StoreColumn
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $this->storeKey = 'store_ids';
        return parent::prepareDataSource($dataSource);
    }
}
