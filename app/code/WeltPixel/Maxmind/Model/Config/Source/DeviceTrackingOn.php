<?php
namespace WeltPixel\Maxmind\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DeviceTrackingOn
 * @package WeltPixel\Maxmin\Model\Config\Source
 */
class DeviceTrackingOn implements ArrayInterface
{

    const TRACK_PRODUCT_PAGES = "product";
    const TRACK_CATEGORY_PAGES = "category";
    const TRACK_CMS_PAGES = "cms";
    const TRACK_OTHER_PAGES = "other";

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TRACK_PRODUCT_PAGES,
                'label' => __('Product Pages')
            ],
            [
                'value' => self::TRACK_CATEGORY_PAGES,
                'label' => __('Category Pages')
            ],
            [
                'value' => self::TRACK_CMS_PAGES,
                'label' => __('Cms Pages')
            ],
            [
                'value' => self::TRACK_OTHER_PAGES,
                'label' => __('Other Pages')
            ],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::TRACK_PRODUCT_PAGES => __('Product Pages'),
            self::TRACK_CATEGORY_PAGES  => __('Category Pages'),
            self::TRACK_CMS_PAGES =>  __('Cms Pages'),
            self::TRACK_OTHER_PAGES => __('Other Pages')
        ];
    }
}
