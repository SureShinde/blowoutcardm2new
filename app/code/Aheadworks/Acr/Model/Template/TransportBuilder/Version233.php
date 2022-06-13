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
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use Magento\Framework\Mail\MimeMessage;
use Magento\Framework\Mail\Template\TransportBuilder;
use Aheadworks\Acr\Model\Template\TransportBuilderInterface;

/**
 * Class Version233
 *
 * @package Aheadworks\Acr\Model\Template\TransportBuilder
 */
class Version233 extends TransportBuilder implements TransportBuilderInterface
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
     * @var MimeMessage|string
     */
    private $content;

    /**
     * @var string
     */
    private $subject;

    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    protected $messageData = [];

    /**
     * @var EmailMessageInterfaceFactory
     */
    protected $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    protected $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    protected $mimePartInterfaceFactory;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->emailMessageInterfaceFactory = $this->objectManager->get(EmailMessageInterfaceFactory::class);
        $this->mimeMessageInterfaceFactory = $this->objectManager->get(MimeMessageInterfaceFactory::class);
        $this->mimePartInterfaceFactory = $this->objectManager->get(MimePartInterfaceFactory::class);
        $this->addressConverter = $this->objectManager->get(AddressConverter::class);
    }

    /**
     * @inheritdoc
     */
    public function addCc($address, $name = '')
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($address, $name = '')
    {
        $this->addAddressByType('to', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($address)
    {
        $this->addAddressByType('bcc', $address);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($email, $name = null)
    {
        $this->addAddressByType('replyTo', $email, $name);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws MailException
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from, null);
    }

    /**
     * @inheritdoc
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);

        return $this;
    }

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
        } elseif ($this->content instanceof MimeMessage) {
            return $this->content->getMessage();
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
     * @inheritdoc
     */
    protected function reset()
    {
        $this->messageData = [];
        return parent::reset();
    }

    /**
     * Prepare message
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate()->setData($this->templateData);
        if ($template->getType() != TemplateTypesInterface::TYPE_TEXT
            && $template->getType() != TemplateTypesInterface::TYPE_HTML
        ) {
            throw new LocalizedException(
                new Phrase('Unknown template type')
            );
        }

        $this->messageData['subject'] = html_entity_decode(
            (string)$template->getSubject(),
            ENT_QUOTES
        );

        $content = $template->getProcessedTemplate($this->templateVars);
        $mimePart = $this->mimePartInterfaceFactory->create(
            ['content' => $content]
        );
        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => [$mimePart]]
        );

        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);
        $this->content = $content;
        $this->subject = $template->getSubject();

        return $this;
    }

    /**
     * Prepare message for preview
     *
     * @return $this
     * @throws LocalizedException
     */
    public function prepareForPreview()
    {
        return $this->prepareMessage();
    }

    /**
     * Handles possible incoming types of email (string or array)
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function addAddressByType(string $addressType, $email, ?string $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $convertedAddressArray = $this->addressConverter->convertMany($email);
        if (isset($this->messageData[$addressType])) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $convertedAddressArray
            );
        }
    }
}
