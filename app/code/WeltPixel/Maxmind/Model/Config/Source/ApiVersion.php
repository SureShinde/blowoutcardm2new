<?php

namespace WeltPixel\Maxmind\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ApiVersion implements ArrayInterface
{
    const MAXMIND_API_LEGACY = 1;
    const MAXMIND_API_SCORE = 2;
    const MAXMIND_API_INSIGHT = 3;
    const MAXMIND_API_FACTORS = 4;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::MAXMIND_API_LEGACY,
                'label' => __('MaxMind minFraud Legacy')
            ],
            [
                'value' => self::MAXMIND_API_SCORE,
                'label' => __('MaxMind minFraud Score')
            ],
            [
                'value' => self::MAXMIND_API_INSIGHT,
                'label' => __('MaxMind minFraud Insight')
            ],
            [
                'value' => self::MAXMIND_API_FACTORS,
                'label' => __('MaxMind minFraud Factors')
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
            self::MAXMIND_API_LEGACY => __('MaxMind minFraud Legacy'),
            self::MAXMIND_API_SCORE  => __('MaxMind minFraud Score'),
            self::MAXMIND_API_INSIGHT =>  __('MaxMind minFraud Insight'),
            self::MAXMIND_API_FACTORS => __('MaxMind minFraud Factors')
        ];
    }
}
