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
namespace Aheadworks\Acr\Ui\Component\Form;

use Magento\Ui\Component\Form\Element\Wysiwyg as UiComponentWysiwyg;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Magento\Backend\Model\UrlInterface;

/**
 * Class Wysiwyg
 * @package Aheadworks\Acr\Ui\Component\Form
 */
class Wysiwyg extends UiComponentWysiwyg
{
    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     * @param array $config
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        UrlInterface $backendUrl,
        array $components = [],
        array $data = [],
        array $config = []
    ) {
        $this->backendUrl = $backendUrl;

        $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];
        $wysiwygConfigData['directives_url'] = $this->backendUrl->getUrl('aw_acr/wysiwyg/directive');
        $wysiwygConfigData['directives_url_quoted'] = $this->backendUrl->getUrl('aw_acr/wysiwyg/directive');
        $config['wysiwygConfigData'] = $wysiwygConfigData;

        parent::__construct($context, $formFactory, $wysiwygConfig, $components, $data, $config);
    }
}
