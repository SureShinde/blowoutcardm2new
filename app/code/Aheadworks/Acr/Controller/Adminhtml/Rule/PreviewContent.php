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
namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

/**
 * Class PreviewContent
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 */
class PreviewContent extends \Aheadworks\Acr\Controller\Adminhtml\AbstractPreviewContent
{
    /**
     * {@inheritdoc}
     */
    public function getPreviewUrl($id = null)
    {
        return $this->urlBuilder->getUrl('aw_acr/rule/preview');
    }
}
