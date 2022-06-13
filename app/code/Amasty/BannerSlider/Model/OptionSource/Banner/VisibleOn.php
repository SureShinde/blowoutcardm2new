<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Banner;

use Magento\Framework\Option\ArrayInterface;

class VisibleOn implements ArrayInterface
{
    const ALL = 0;

    const DESKTOP = 1;

    const MOBILE = 2;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL,
                'label' => __('Desktop and Mobile')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('Desktop Only')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Mobile Only')
            ]
        ];
    }
}
