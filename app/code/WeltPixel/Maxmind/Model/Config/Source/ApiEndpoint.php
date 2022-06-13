<?php

namespace WeltPixel\Maxmind\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ApiEndpoint implements ArrayInterface
{
    const ENDPOINT_DEFAULT = "minfraud.maxmind.com";
    const ENDPOINT_US_EAST = "minfraud-us-east.maxmind.com";
    const ENDPOINT_US_WEST= "minfraud-us-west.maxmind.com";
    const ENDPOINT_EU_WEST = "minfraud-eu-west.maxmind.com";

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENDPOINT_DEFAULT,
                'label' => __('Automatically Detect')
            ],
            [
                'value' => self::ENDPOINT_US_EAST,
                'label' => __('US East, Virginia, US')
            ],
            [
                'value' => self::ENDPOINT_US_WEST,
                'label' => __('US West, San Jose, California, US')
            ],
            [
                'value' => self::ENDPOINT_EU_WEST,
                'label' => __('EU West, London, UK')
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
            self::ENDPOINT_DEFAULT => __('Automatically Detect'),
            self::ENDPOINT_US_EAST  => __('US East, Virginia, US'),
            self::ENDPOINT_US_WEST =>  __('US West, San Jose, California, US'),
            self::ENDPOINT_EU_WEST => __('EU West, London, UK')
        ];
    }
}
