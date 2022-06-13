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
namespace Aheadworks\Acr\Model\Source\Queue;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Acr\Model\Source\Queue
 */
class Status implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => QueueInterface::STATUS_PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => QueueInterface::STATUS_SENT,
                'label' => __('Sent')
            ],
            [
                'value' => QueueInterface::STATUS_FAILED,
                'label' => __('Failed')
            ],
            [
                'value' => QueueInterface::STATUS_CANCELLED,
                'label' => __('Cancelled')
            ],
        ];
    }
}
