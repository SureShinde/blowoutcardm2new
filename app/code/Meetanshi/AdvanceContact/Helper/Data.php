<?php

namespace Meetanshi\AdvanceContact\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Meetanshi\AdvanceContact\Model\AdvanceContactFactory;
use Meetanshi\AdvanceContact\Model\ContactListFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;

class Data extends AbstractHelper
{

    const ADVANCE_CONTACT_ENABLED = 'advance_contact/general/enabled';
    const DEFAULT_CONTACT_EMAIL = 'contact/email/recipient_email';

    private $storeManagerInterface;
    private $advanceContactFactory;
    private $contactListFactory;
    protected $messageManager;
    private $contactsConfig;
    private $transportBuilder;
    private $inlineTranslation;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AdvanceContactFactory $advanceContactFactory,
        ContactListFactory $contactListFactory,
        ManagerInterface $messageManager,
        ConfigInterface $contactsConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {

        $this->storeManagerInterface = $storeManager;
        $this->advanceContactFactory = $advanceContactFactory;
        $this->contactListFactory = $contactListFactory;
        $this->messageManager = $messageManager;
        $this->contactsConfig = $contactsConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    public function getConfig($value)
    {
        return $this->scopeConfig->getValue($value, ScopeInterface::SCOPE_STORE);
    }

    public function isEnable()
    {
        return $this->getConfig(self::ADVANCE_CONTACT_ENABLED);
    }

    public function getDefaultContact(){
        return $this->getConfig(self::DEFAULT_CONTACT_EMAIL);
    }

    public function getDepartment()
    {
        if (!$this->isEnable()) {
            return false;
        }
        $department = $this->advanceContactFactory->create();
        $collection = $department->getCollection()
                      ->addFieldToFilter('is_active', 1);
        if ($collection->count()) {
            return $collection->getData();
        }
    }

    public function sendMail($dptData)
    {
        $department = $this->advanceContactFactory->create();
        $collection = $department->getCollection()
            ->addFieldToFilter('id', $dptData['department']);

        if ($collection->count()) {
            foreach ($collection->getData() as $data) {
                $dptEmail = explode(',', $data['email']);
                foreach ($dptEmail as $email) {
                    $this->send($dptData['email'], ['data' => new DataObject($dptData)], $email);
                }
                $this->saveContact($data['name'], ['data' => new DataObject($dptData)]);
                $this->messageManager->addSuccessMessage(__("Contact Message sent to ".$data['name']." Department"));
            }
        }
    }

    public function send($replyTo, array $variables, $email)
    {
        $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;

        $defaultContactEmail = $this->getDefaultContact();

        if ($replyTo != $defaultContactEmail) {
            $this->inlineTranslation->suspend();
            try {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->contactsConfig->emailTemplate())
                    ->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND,
                            'store' => $this->storeManagerInterface->getStore()->getId()
                        ]
                    )
                    ->setTemplateVars($variables)
                    ->setFrom($this->contactsConfig->emailSender())
                    ->addTo($email)
                    ->setReplyTo($replyTo, $replyToName)
                    ->getTransport();

                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } finally {
                $this->inlineTranslation->resume();
            }
        }
    }
    public function saveContact($name, $variables)
    {
        $model = $this->contactListFactory->create();

        $model->addData([
            "name" => $variables['data']['name'],
            "email" => $variables['data']['email'],
            "comment" => $variables['data']['comment'],
            "department" => $name
        ]);
        $model->save();
    }
}
