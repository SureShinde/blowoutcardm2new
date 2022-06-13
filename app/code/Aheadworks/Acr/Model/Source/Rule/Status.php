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
 * Class Status
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class Status implements OptionSourceInterface
{
    /**
     * 'Enabled' status
     */
    const ENABLED = 1;

    /**
     * 'Disabled' status
     */
    const DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DISABLED,
                'label' => __('Disabled')
            ],
            [
                'value' => self::ENABLED,
                'label' => __('Enabled')
            ]
        ];
    }
}
