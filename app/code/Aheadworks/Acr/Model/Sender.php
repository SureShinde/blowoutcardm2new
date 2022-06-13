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
namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Template\TransportBuilder\Factory as TransportBuilderFactory;
use Aheadworks\Acr\Model\Template\TransportBuilderInterface;
use Aheadworks\Acr\Model\Exception\TestRecipientNotSpecified;
use Magento\Framework\Exception\MailException;
use Aheadworks\Acr\Model\Template\VariableProvider;
use Aheadworks\Acr\Model\Email\Content;

/**
 * Class Sender
 * @package Aheadworks\Acr\Model
 */
class Sender
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TransportBuilderInterface
     */
    private $transportBuilder;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var variableProvider
     */
    private $variableProvider;

    /**
     * @var Content
     */
    private $content;

    /**
     * @param Config $config
     * @param TransportBuilderFactory $transportBuilderFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param VariableProvider $variableProvider
     * @param Content $content
     */
    public function __construct(
        Config $config,
        TransportBuilderFactory $transportBuilderFactory,
        RuleRepositoryInterface $ruleRepository,
        VariableProvider $variableProvider,
        Content $content
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilderFactory->create();
        $this->ruleRepository = $ruleRepository;
        $this->variableProvider = $variableProvider;
        $this->content = $content;
    }

    /**
     * Send test email
     *
     * @param QueueInterface $queueItem
     * @throws TestRecipientNotSpecified
     * @return $queueItem
     */
    public function sendTestEmail(QueueInterface $queueItem)
    {
        $recipientEmail = $this->config->getTestEmailRecipient($queueItem->getStoreId());
        if (!$recipientEmail) {
            throw new TestRecipientNotSpecified(
                __('Unable to send test email. Test Email Recipient is not specified.')
            );
        }
        $emailData = $this->variableProvider->getTestTemplateVarsData($queueItem->getStoreId());
        $recipientName = isset($emailData['customer_name']) ? $emailData['customer_name'] : '';
        /** @var RuleInterface $rule */
        $rule = $this->ruleRepository->get($queueItem->getRuleId());

        $result = $this->sendEmail(
            $recipientEmail,
            $recipientName,
            '[TEST EMAIL] '. $rule->getSubject(),
            $this->content->getFullContent($rule->getContent(), $queueItem->getStoreId()),
            $queueItem->getStoreId(),
            $emailData
        );
        $queueItem
            ->setRecipientEmail($recipientEmail)
            ->setRecipientName($recipientName)
            ->setSavedSubject($result['subject'])
            ->setSavedContent($result['content']);

        return $queueItem;
    }

    /**
     * Send queue item
     *
     * @param QueueInterface $queueItem
     * @return QueueInterface
     * @throws MailException
     */
    public function sendQueueItem(QueueInterface $queueItem)
    {
        $storeId = $queueItem->getStoreId();
        if ($this->config->isTestModeEnabled($storeId)) {
            $recipientEmail = $this->config->getTestEmailRecipient();
        } else {
            $recipientEmail = $queueItem->getRecipientEmail();
        }
        if ($queueItem->getSavedContent()) {
            $this->sendEmail(
                $recipientEmail,
                $queueItem->getRecipientName(),
                $queueItem->getSavedSubject(),
                $queueItem->getSavedContent(),
                $queueItem->getStoreId()
            );
        } else {
            $emailData = $this->variableProvider->getTemplateVarsData($queueItem);

            $rule = $this->ruleRepository->get($queueItem->getRuleId());
            $this->sendEmail(
                $recipientEmail,
                $queueItem->getRecipientName(),
                $rule->getSubject(),
                $this->content->getFullContent($rule->getContent(), $storeId),
                $queueItem->getStoreId(),
                $emailData
            );
            $queueItem
                ->setSavedSubject($this->transportBuilder->getMessageSubject())
                ->setSavedContent($this->transportBuilder->getMessageContent());
            if ($this->config->isTestModeEnabled($storeId)) {
                $queueItem->setRecipientEmail($recipientEmail);
            }
        }
        return $queueItem;
    }

    /**
     * Get prepared content for preview (test data)
     *
     * @param int $storeId
     * @param string $subject
     * @param string $content
     * @return array ('subject' => ..., 'content' => ...)
     */
    public function getTestPreview($storeId, $subject, $content)
    {
        $recipientEmail = $this->config->getTestEmailRecipient($storeId) ?
            $this->config->getTestEmailRecipient($storeId) :
            'recipient@example.com';

        $emailData = $this->variableProvider->getTestTemplateVarsData($storeId);
        $recipientName = isset($emailData['customer_name']) ? $emailData['customer_name'] : '';
        $this->transportBuilder
            ->setTemplateModel(Template::class)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ])
            ->setTemplateVars($emailData)
            ->setTemplateData([
                'template_subject' => $subject,
                'template_text' => $this->content->getFullContent($content, $storeId)
            ])
            ->addTo($recipientEmail, $recipientName)
        ;

        $this->transportBuilder->prepareForPreview();

        $result = [];
        $result['recipient_name'] = $recipientName;
        $result['recipient_email'] = $recipientEmail;
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }

    /**
     * Get prepared content for preview
     *
     * @param QueueInterface $queueItem
     * @param string $subject
     * @param string $content
     * @return array ('subject' => ..., 'content' => ...)
     */
    public function getPreview(QueueInterface $queueItem, $subject, $content)
    {
        $emailData = $this->variableProvider->getTemplateVarsData($queueItem);

        $this->transportBuilder
            ->setTemplateModel(Template::class)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $queueItem->getStoreId()
            ])
            ->setTemplateVars($emailData)
            ->setTemplateData([
                'template_subject' => $subject,
                'template_text' => $this->content->getFullContent($content, $queueItem->getStoreId())
            ])
            ->addTo($queueItem->getRecipientEmail(), $queueItem->getRecipientName())
        ;

        $this->transportBuilder->prepareForPreview();

        $result = [];
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }

    /**
     * Send email
     *
     * @param string $recipientEmail
     * @param string $recipientName
     * @param string $subject
     * @param string $content
     * @param int $storeId
     * @param array $emailData
     * @return array (['subject' => ..., 'content' => ...])
     */
    public function sendEmail($recipientEmail, $recipientName, $subject, $content, $storeId, $emailData = [])
    {
        $this->transportBuilder
            ->setTemplateModel(Template::class)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )->setTemplateVars($emailData)
            ->setTemplateData(
                [
                    'template_subject' => $subject,
                    'template_text' => $content
                ]
            )->setFrom(
                [
                    'name' => $this->config->getSenderName($storeId),
                    'email' => $this->config->getSenderEmail($storeId)
                ]
            )->addTo($recipientEmail, $recipientName);

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $result = [];
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }
}
