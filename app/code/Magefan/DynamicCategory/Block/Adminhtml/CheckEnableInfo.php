<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Block\Adminhtml;

use Magento\Framework\App\ObjectManager;
use Magefan\Community\Model\Section;

/**
 * Class Check EnableInfo Block
 */
class CheckEnableInfo extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * CheckEnableInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magefan\DynamicCategory\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magefan\DynamicCategory\Model\Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }
}
