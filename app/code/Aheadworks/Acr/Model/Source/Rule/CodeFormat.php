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
namespace Aheadworks\Acr\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;

/**
 * Class CodeFormat
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class CodeFormat implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHANUMERIC,
                'label' => __('Alphanumeric')
            ],
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHABETICAL,
                'label' => __('Alphabetical')
            ],
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_NUMERIC,
                'label' => __('Numeric')
            ]
        ];
    }
}
