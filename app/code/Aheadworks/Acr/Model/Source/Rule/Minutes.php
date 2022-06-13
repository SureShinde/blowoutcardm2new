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
 * Class Minutes
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class Minutes implements OptionSourceInterface
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
            $units = __('minutes');
            for ($minutes = 0; $minutes < 60; $minutes += 5) {
                $this->options[] = [
                    'value' => $minutes,
                    'label' => $minutes . ' ' . $units
                ];
            }
        }
        return $this->options;
    }
}
