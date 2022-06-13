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
namespace Aheadworks\Acr\Model\Email\Template;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation as AppEmulation;

/**
 * Class Content
 * @package Aheadworks\Acr\Model\Email\Template
 */
class Content
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @param TemplateFactory $templateFactory
     * @param AppEmulation $appEmulation
     */
    public function __construct(
        TemplateFactory $templateFactory,
        AppEmulation $appEmulation
    ) {
        $this->templateFactory = $templateFactory;
        $this->appEmulation = $appEmulation;
    }

    /**
     * Get template content
     *
     * @param int|string $templateId
     * @param int $storeId
     * @return string
     */
    public function getTemplateContent($templateId, $storeId)
    {
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $template = $this->templateFactory->create();
        if (is_numeric($templateId)) {
            $template->load($templateId);
        } else {
            $template->loadDefault($templateId);
        }
        $templateContent = $template->getTemplateText();
        $this->appEmulation->stopEnvironmentEmulation();
        return $templateContent;
    }
}
