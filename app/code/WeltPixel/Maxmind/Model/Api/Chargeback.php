<?php

namespace WeltPixel\Maxmind\Model\Api;

use Magento\Sales\Model\Order;

/**
 * Class Chargeback
 * @package WeltPixel\Maxmind\Model\Api
 */
class Chargeback extends Score
{
    /**
     * @var string
     */
    protected $apiType = 'chargeback';

    /**
     * @var string
     */
    protected $basePath = '/minfraud/';

    /**
     * @param Order $order
     * @param array $params
     * @return array|bool|mixed|string
     */
    public function makeChargeBackCall($order, $params)
    {
        $storeId = $order->getStoreId();
        $this->licenseKey = $this->maxmindHelper->getLicenseKey($storeId);
        $this->accountId = $this->maxmindHelper->getAccountId($storeId);
        $this->order = $order;

        $this->maxmindModel->loadMaxmindByOrderId($this->order->getId());
        $sentData = $this->serializer->unserialize($this->maxmindModel->getSentData());
        $fraudData =  $this->serializer->unserialize(utf8_decode($this->maxmindModel->getFraudData()));
        $apiVersion = $this->maxmindModel->getApiVersion();

        if (empty($this->licenseKey)) {
            return [
                "errmsg" => __("Maxmind License key not set."),
                "err" => "FATAL_ERR"
            ];
        }

        if (empty($this->accountId)) {
            return [
                "errmsg" => __("Maxmind Account Id is not set."),
                "err" => "FATAL_ERR"
            ];
        }

        $maxmindRequestData = [];
        switch ($apiVersion) {
            case 2:
            case 3:
            case 4:
                $maxmindRequestData['ip_address'] = $sentData['device']['ip_address'];
                $maxmindRequestData['transaction_id'] = $sentData['event']['transaction_id'];
                $maxmindRequestData['minfraud_id'] = $fraudData['id'];
                break;
            default:
                $maxmindRequestData['ip_address'] = $sentData['i'];
                $maxmindRequestData['transaction_id'] = $sentData['txnID'];
                $maxmindRequestData['maxmind_id'] = $fraudData['maxmindID'];
                break;
        }

        if (isset($params['chargeback_code']) && strlen(trim($params['chargeback_code']))) {
            $maxmindRequestData['chargeback_code'] = trim($params['chargeback_code']);
        }

        if (isset($params['chargeback_tag']) && strlen(trim($params['chargeback_tag']))) {
            $maxmindRequestData['tag'] = trim($params['chargeback_tag']);
        }

        $requestBody = json_encode($maxmindRequestData);
        if ($requestBody === false) {
            return [
                "errmsg" => __('Request Api Json Encoding Error'),
                "err" => "FATAL_ERR"
            ];
        }

        $result = $this->_makeApiCall($this->getApiEndpoint($storeId), $requestBody);

        return $result;
    }
}
