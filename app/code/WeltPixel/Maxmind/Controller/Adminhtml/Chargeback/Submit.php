<?php

namespace WeltPixel\Maxmind\Controller\Adminhtml\Chargeback;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\OrderFactory;
use WeltPixel\Maxmind\Model\Api\Chargeback as MaxmindChargeback;
use WeltPixel\Maxmind\Model\Maxmind as MaxmindModel;

/**
 * Class Submit
 * @package WeltPixel\Maxmind\Controller\Adminhtml\Chargeback
 */
class Submit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var MaxmindChargeback
     */
    protected $maxmindChargeback;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var MaxmindModel
     */
    private $maxmindModel;


    /**
     * Version constructor.
     *
     * @param Context $context
     * @param MaxmindChargeback $maxmindChargeback
     * @param OrderFactory $orderFactory
     * @param JsonFactory $resultJsonFactory
     * @param MaxmindModel #maxmindModel
     */
    public function __construct(
        Context $context,
        MaxmindChargeback $maxmindChargeback,
        OrderFactory $orderFactory,
        JsonFactory $resultJsonFactory,
        MaxmindModel $maxmindModel
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->maxmindChargeback = $maxmindChargeback;
        $this->orderFactory = $orderFactory;
        $this->maxmindModel = $maxmindModel;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $msg = [];
        $params = $this->getRequest()->getParams();
        $orderId = $this->getRequest()->getParam('order_id', null);

        if (isset($orderId)) {
            $order = $this->orderFactory->create()->load($orderId);
            $result = $this->maxmindChargeback->makeChargeBackCall($order, $params);
            if (!isset($result['err'])) {
                $msg['msg'] = $result['msg'];
                $msg['sent'] = 1;
                $this->maxmindModel->loadMaxmindByOrderId($orderId)
                    ->addData([
                        'chargeback_flag' => 1,
                    ])
                    ->save();
            } else {
                $msg['msg'] = $result['errmsg'];
                $msg['sent'] = 0;
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($msg);
        return $resultJson;
    }
}
