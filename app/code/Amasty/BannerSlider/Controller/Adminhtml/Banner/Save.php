<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Banner;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_BannerSlider::banners_banner';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\BannerSlider\Model\Repository\BannerRepository
     */
    private $bannerRepository;

    /**
     * @var \Amasty\BannerSlider\Model\BannerFactory
     */
    private $bannerFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Model\Repository\BannerRepository $bannerRepository,
        \Amasty\BannerSlider\Model\BannerFactory $bannerFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\DataObject $dataObject,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        $this->logger = $logger;
        $this->bannerRepository = $bannerRepository;
        $this->bannerFactory = $bannerFactory;
    }

    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getPostValue()) {
            /** @var Banner $model */
            $model = $this->bannerFactory->create();

            try {
                if ($bannerId = (int) $this->getRequest()->getParam('id')) {
                    $model = $this->bannerRepository->getById($bannerId);
                }

                $data = $this->prepareData($data);
                $model->setData($data);
                $this->bannerRepository->save($model);

                $this->messageManager->addSuccessMessage(__('The Banner was successfully saved.'));
                $this->dataPersistor->clear(Banner::PERSIST_NAME);

                if ($this->getRequest()->getParam('back')) {
                    return $redirect->setPath('ambannerslider/*/edit', [
                        'id' => $model->getId(),
                        'store' => (int)$this->_request->getParam('store_id', Store::DEFAULT_STORE_ID)
                    ]);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($bannerId) {
                    $redirect->setPath('ambannerslider/*/edit', ['id' => $bannerId]);
                } else {
                    $redirect->setPath('ambannerslider/*/newAction');
                }

                return $redirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the banner data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->dataPersistor->set(Banner::PERSIST_NAME, $data);

                return $redirect->setPath('ambannerslider/*/edit', ['id' => $bannerId]);
            }
        }

        return $redirect->setPath('ambannerslider/*/');
    }

    private function prepareData(array $data): array
    {
        if (isset($data[BannerInterface::ID])) {
            $data[BannerInterface::ID] = (int) $data[BannerInterface::ID] ?: null;
        }
        if (isset($data[BannerInterface::CUSTOMER_GROUP]) && is_array($data[BannerInterface::CUSTOMER_GROUP])) {
            $data[BannerInterface::CUSTOMER_GROUP] = implode(',', $data[BannerInterface::CUSTOMER_GROUP]);
        }
        if (isset($data[BannerInterface::IMAGE][0]['name'])) {
            $data[BannerInterface::IMAGE] = $data[BannerInterface::IMAGE][0]['name'];
        }

        return $data;
    }
}
