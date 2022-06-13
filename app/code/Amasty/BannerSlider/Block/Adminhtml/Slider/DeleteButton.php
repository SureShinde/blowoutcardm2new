<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Adminhtml\Slider;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $sliderId = $this->getSliderId();
        if ($sliderId) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\''
                    . __('Are you sure you want to delete this?') . '\', \''
                    . $this->getUrlBuilder()->getUrl('*/*/delete', ['id' => $sliderId]) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
