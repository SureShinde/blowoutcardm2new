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
namespace Aheadworks\Acr\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class Stores
 * @package Aheadworks\Acr\Model\Source
 */
class Stores extends StoreOptions implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $allStores = [
            'label' => __('All Store Views'),
            'value' => 0
        ];
        $this->currentOptions['All Store Views'] = $allStores;

        return parent::toOptionArray();
    }
}
