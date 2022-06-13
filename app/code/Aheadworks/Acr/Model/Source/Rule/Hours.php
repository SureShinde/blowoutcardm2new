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

/**
 * Class Hours
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class Hours implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $units = __('hours');
            $unitsSingle = __('hour');
            for ($hour = 0; $hour < 24; $hour++) {
                $this->options[] = [
                    'value' => $hour,
                    'label' => $hour . ' ' . ($this->useSingleUnit($hour) ? $unitsSingle : $units)
                ];
            }
        }
        return $this->options;
    }

    /**
     * Use single unit
     *
     * @param int $value
     * @return bool
     */
    private function useSingleUnit($value)
    {
        return in_array($value, [1]);
    }
}
