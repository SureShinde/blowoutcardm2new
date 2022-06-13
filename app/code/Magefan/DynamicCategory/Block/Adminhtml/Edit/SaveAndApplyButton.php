<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Block\Adminhtml\Edit;

use Magefan\Community\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

class SaveAndApplyButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(Context $context, \Magento\Framework\AuthorizationInterface $authorization = null)
    {
        parent::__construct($context, $authorization);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getObjectId()) {
            $data = [
                'label' => __('Save and Apply'),
                'class' => 'save',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'Magento_Ui/js/form/button-adapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'mfdynamiccategory_rule_form.mfdynamiccategory_rule_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['auto_apply' => 1],
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'sort_order' => 80,
            ];
        }

        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getSaveApplyUrl()
    {
        return $this->getUrl('*/*/saveAndApply', ['id' => $this->getObjectId()]);
    }
}
