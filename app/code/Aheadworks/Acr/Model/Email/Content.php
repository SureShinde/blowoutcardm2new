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
namespace Aheadworks\Acr\Model\Email;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Model\Email\Template\Content as TemplateContent;

/**
 * Class Content
 * @package Aheadworks\Acr\Model\Email
 */
class Content
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TemplateContent
     */
    private $templateContent;

    /**
     * @param Config $config
     * @param TemplateContent $templateContent
     */
    public function __construct(
        Config $config,
        TemplateContent $templateContent
    ) {
        $this->config = $config;
        $this->templateContent = $templateContent;
    }

    /**
     * Get Full Content
     *
     * @param string $content
     * @param int $storeId
     * @return string
     */
    public function getFullContent($content, $storeId)
    {
        $header = $this->templateContent->getTemplateContent($this->config->getHeaderTemplateId($storeId), $storeId);
        $footer = $this->templateContent->getTemplateContent($this->config->getFooterTemplateId($storeId), $storeId);
        return $header . $content . $footer;
    }
}
