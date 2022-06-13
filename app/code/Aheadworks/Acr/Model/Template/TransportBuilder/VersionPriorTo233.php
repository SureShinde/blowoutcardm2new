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
namespace Aheadworks\Acr\Model\Template\TransportBuilder;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Aheadworks\Acr\Model\Template\TransportBuilderInterface;

/**
 * Class VersionPriorTo233
 *
 * @package Aheadworks\Acr\Model\Template\TransportBuilder
 */
class VersionPriorTo233 extends TransportBuilder implements TransportBuilderInterface
{
    /**
     * Template data
     *
     * @var array
     */
    private $templateData = [];

    /**
     * @var string
     */
    private $messageType = MessageInterface::TYPE_HTML;

    /**
     * @var \Zend_Mime_Part|string
     */
    private $content;

    /**
     * @var string
     */
    private $subject;

    /**
     * Set template data
     *
     * @param array $data
     * @return $this
     */
    public function setTemplateData($data)
    {
        $this->templateData = $data;
        return $this;
    }

    /**
     * Set message type
     *
     * @param string $messageType
     * @return $this
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
        return $this;
    }

    /**
     * Get message content
     *
     * @return string
     */
    public function getMessageContent()
    {
        if ($this->content instanceof \Zend_Mime_Part) {
            return $this->content->getRawContent();
        } elseif ($this->content instanceof \Zend\Mime\Message) {
            return $this->content->generateMessage();
        } else {
            return $this->content;
        }
    }

    /**
     * Get message subject
     *
     * @return string
     */
    public function getMessageSubject()
    {
        return $this->subject;
    }

    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate()->setData($this->templateData);

        $this->message->setMessageType(
            $this->messageType
        )->setBody(
            $template->getProcessedTemplate($this->templateVars)
        )->setSubject(
            $template->getSubject()
        );
        $this->content = $this->message->getBody();
        $this->subject = $template->getSubject();

        return $this;
    }

    /**
     * Prepare message for preview
     *
     * @return $this
     */
    public function prepareForPreview()
    {
        return $this->prepareMessage();
    }
}
