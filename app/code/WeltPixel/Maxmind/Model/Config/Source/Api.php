<?php

namespace WeltPixel\Maxmind\Model\Config\Source;

class Api implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Automatically Detect'),
                'value' => 'https://minfraud.maxmind.com/app/ccv2r',
            ],
            [
                'label' => __('US-east'),
                'value' => 'https://minfraud-us-east.maxmind.com/app/ccv2r',
            ],
            [
                'label' => __('US-west'),
                'value' => 'https://minfraud-us-west.maxmind.com/app/ccv2r',
            ],
            [
                'label' => __('EU-west'),
                'value' => 'https://minfraud-eu-west.maxmind.com/app/ccv2r',
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
            'https://minfraud.maxmind.com/app/ccv2r'          => __('Automatically Detect'),
            'https://minfraud-us-east.maxmind.com/app/ccv2r'  => __('US-east'),
            'https://minfraud-us-west.maxmind.com/app/ccv2r'  => __('US-west'),
            'https://minfraud-eu-west.maxmind.com/app/ccv2r'  => __('EU-west'),
        ];
    }
}
