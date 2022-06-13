<?php

namespace Meetanshi\AdvanceContact\Block\Adminhtml\AdvanceContact\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class GenericButton
{
    protected $urlBuilder;
    protected $registry;

    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    public function getId()
    {
        $contact = $this->registry->registry('general');
        return $contact ? $contact->getId() : null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
