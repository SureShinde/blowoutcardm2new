<?php

namespace WeltPixel\Maxmind\Helper;

use Magento\Directory\Model\Region;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Variable\Model\Variable;
use WeltPixel\Maxmind\Model\Maxmind as MaxmindModel;

/**
 * Helper Data
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */

class Data extends AbstractHelper
{
    const MAXMIND_VARIABLE_NAME = 'maxmind_remaining_credit';

    /**
     * @var array
     */
    private $_requiredFields = ["i", "license_key", "country", "city", "postal"];

    /**
     * @var MaxmindModel
     */
    private $_maxmindModel;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var Quote
     */
    private $_quote;

    /**
     * @var Variable
     */
    private $_variableModel;

    /**
     * @var DateTime
     */
    private $_date;

    /**
     * @var Serialize
     */
    private $_serializer;

    /**
     * @var Monolog
     */
    private $_monolog;

    /**
     * @var Region
     */
    private $_regionModel;

    /**
     * [__construct description].
     *
     * @param Context $context
     * @param MaxmindModel $maxmindModel
     * @param DateTime $date
     * @param Quote $quote
     * @param Variable $variableModel
     * @param Region $regionModel
     * @param Monolog $monolog
     * @param Serialize $serialize
     */
    public function __construct(
        Context $context,
        MaxmindModel $maxmindModel,
        DateTime $date,
        Quote $quote,
        Variable $variableModel,
        Region $regionModel,
        Monolog $monolog,
        Serialize $serialize
    ) {
        parent::__construct($context);

        $this->_maxmindModel = $maxmindModel;
        $this->_date = $date;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_quote = $quote;
        $this->_variableModel = $variableModel;
        $this->_regionModel = $regionModel;
        $this->_monolog = $monolog;
        $this->_serializer = $serialize;
    }

    /**
     * Check maxmind data
     *
     * @param array $response
     * @return array
     */
    public function checkMaxmindData($response)
    {
        if (array_key_exists('riskScore', $response) && $response['riskScore'] != '') {
            $response['ourscore'] = floatval($response['riskScore']);
        } else {
            $response['err'] = (array_key_exists('err', $response) && !empty($res['err']))
                ? $response['err']
                : "FATAL_ERR";
            $response['errmsg'] = array_key_exists('errmsg', $response)
                ? $response['errmsg']
                : "No riskScore or server response.";
        }

        return $response;
    }

    /**
     * Get the Maxmind server response
     *
     * @param array $params
     * @param int $storeId
     * @return array
     */
    public function getMaxmindServerResponse($params, $storeId)
    {
        $return = [];

        $url = $this->getConfigValue('connection/api_server', $storeId);

        try {
            $curl_options = [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,

                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTP_VERSION => 1.0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false
            ];

            $curl = curl_init();

            if ($this->getConfigValue('connection/disable_certificate_check', $storeId)) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            curl_setopt_array($curl, $curl_options);
            $response = curl_exec($curl);

            curl_close($curl);

            if (!empty($response)) {
                foreach (explode(";", $response) as $keyval) {
                    $bits = explode("=", $keyval);
                    if (count($bits) > 1) {
                        list($key, $value) = $bits;
                        $return[$key] = $value;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_monolog->addError($e->getMessage());
        }

        if (!count($return)) {
            $return['errmsg'] = "Could not connect to MaxMind server.";
            $return['err'] = "FATAL_ERR";
        }

        return $return;
    }

    /**
     * Get maxmind data
     * @param $payment
     * @param int $storeId
     * @return array
     * @throws \Exception
     */
    public function getMaxmindData($payment, $storeId)
    {
        $order = $payment->getOrder();

        $params = $this->getMaxmindParams($payment, $storeId);

        if (!isset($params['i']) || empty($params['i'])) {
            return [
                "err" => "FATAL_ERR",
                "errmsg" => "Order IP address not found."
            ];
        }

        foreach ($this->_requiredFields as $field) {
            if (!array_key_exists($field, $params) || empty($params[$field])) {
                return [
                    "errmsg" => "Missing $field required field.",
                    "err" => "FATAL_ERR"
                ];
            }
        }

        $ipExceptions = $this->getIpExceptions($storeId);
        if ($ipExceptions) {
            $ipExceptions = explode(',', $ipExceptions);

            if (in_array($params['i'], $ipExceptions)) {
                return [
                    "errmsg" => "IP address " . $params['i'] . " is in IP exceptions list.",
                    "err" => "FATAL_ERR"
                ];
            }
        }

        if ($order->getId()) {
            $this->_maxmindModel->loadMaxmindByOrderId($order->getId())
                ->addData([
                    'order_id' => $order->getId(),
                    'sent_data' => utf8_encode($this->_serializer->serialize($params))
                ])
                ->save();
        } else {
            $order->setMaxmindTempData(utf8_encode($this->_serializer->serialize($params)));
        }

        $response = $this->getMaxmindServerResponse($params, $order->getStoreId());

        // set remaining maxmind credit in custom variable
        if (!empty($response['queriesRemaining'])) {
            $model = $this->_variableModel->loadByCode(self::MAXMIND_VARIABLE_NAME);

            if (!$model->getCode()) {
                $model->setCode(self::MAXMIND_VARIABLE_NAME);
                $model->setName('Maxmind Remaining Credit ');
                $model->setPlainValue($response['queriesRemaining']);
            } else {
                $model->setPlainValue($response['queriesRemaining']);
            }

            $model->save();
        }

        return $response;
    }

    /**
     * Prepare maxmind params
     *
     * @param $payment
     * @param int $storeId
     * @return mixed
     */
    public function getMaxmindParams($payment, $storeId)
    {
        $order = $payment->getOrder();

        $billAddress = $order->getBillingAddress();
        $shipAddress = $order->getShippingAddress();

        if (!($billAddress && $billAddress->getCountry()) || !($shipAddress && $shipAddress->getCountry())) {
            $shipAddress = $shipAddress && $shipAddress->getCountry() ? $shipAddress : $billAddress;

            $address = $billAddress && $billAddress->getCountry() ? $billAddress : $shipAddress;
        }
        if (!$billAddress && !$shipAddress) {
            return [
                "errmsg" => "Billing or Shipping address not found.",
                "err" => "FATAL_ERR"
            ];
        }

        $licenseKey = trim($this->getLicenseKey($storeId));

        if (empty($licenseKey)) {
            return [
                "errmsg" => "License key not set.",
                "err" => "FATAL_ERR"
            ];
        }

        $params["requested_type"] = 'standard';
        $params["license_key"] = trim($licenseKey);
        $params["i"] = $this->getOrderRemoteIp($order);

        if ($this->getForceIp($storeId)) {
            $params["i"] = $this->getForceIp($storeId);
        }

        $params["city"] = $address->getCity();
        $params["region"] = $address->getRegion();
        $params["postal"] = $address->getPostcode();
        $params["country"] = $address->getCountryId();

        if ((strlen($params["postal"]) > 5) && (strpos($params["postal"], '-') !== false) &&
            $params["country"] == "US"
        ) {
            $params["postal"] = substr($address->getPostcode(), 0, 5);
        }

        $regionCode = '';
        if ($address->getRegionId()) {
            $regionCode = $this->getRegionCode($address->getRegionId());
        }

        $params["region"] = $regionCode;

        if (preg_match("/[^@]+@(.*)/", $order->getCustomerEmail(), $matchEmail)) {
            $params["domain"] = $matchEmail[1];
        }

        if ($payment->getCcNumber()) {
            $params["bin"] = substr($payment->getCcNumber(), 0, 6);
        }

        $params["usernameMD5"] = hash('MD5', (strtolower($order->getCustomerEmail())));
        $params["passwordMD5"] = $this->_quote->load($order->getQuoteId())->getPasswordHash();
        $params["emailMD5"] = hash('MD5', (strtolower($order->getCustomerEmail())));
        $params["custPhone"] = $address->getTelephone();
        $params["shipAddr"] = implode(',', $shipAddress->getStreet());
        $params["shipCity"] = $shipAddress->getCity();

        $regionCode = '';
        if ($shipAddress->getRegion()) {
            $regionCode = $this->getRegionCode($shipAddress->getRegion());
        }
        $params["shipRegion"] = $regionCode;
        $params["shipPostal"] = $shipAddress->getPostcode();
        $params["shipCountry"] = $shipAddress->getCountryId();

        if ((strlen($params["shipPostal"]) > 5) && (strpos($params["shipPostal"], '-') !== false) &&
            $params["shipCountry"] == "US"
        ) {
            $params["shipPostal"] = substr($shipAddress->getPostcode(), 0, 5);
        }

        $params["txnID"] = $order->getIncrementId();
        $params["accept_language"] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $params["user_agent"] = $_SERVER['HTTP_USER_AGENT'];

        return $params;
    }

    /**
     * Return region code by region id
     *
     * @param $regionId
     * @return mixed
     */
    public function getRegionCode($regionId)
    {
        $region = $this->_regionModel->load($regionId);

        return $region->getCode();
    }

    /**
     * Save maxmind data
     * @param $data
     * @param $order
     * @throws \Exception
     */
    public function saveMaxmindData($data, $order)
    {
        $apiVersion = $this->getApiVersion($order->getStoreId());
        if ($order->getMaxmindTempData()) {
            $this->_maxmindModel
                ->loadMaxmindByOrderId($order->getId())
                ->addData([
                    'order_id' => $order->getId(),
                    'fraud_score' => $data['ourscore'],
                    'fraud_data' => utf8_encode($this->_serializer->serialize($data)),
                    'sent_data' => $order->getMaxmindTempData(),
                    'api_version' => $apiVersion
                ])
                ->save();
        } else {
            $this->_maxmindModel
                ->loadMaxmindByOrderId($order->getId())
                ->addData([
                    'order_id' => $order->getId(),
                    'fraud_score' => $data['ourscore'],
                    'fraud_data' => utf8_encode($this->_serializer->serialize($data)),
                ])
                ->save();
        }
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function moduleEnabled($storeId = null)
    {
        return $this->getConfigValue('general/enable', $storeId);
    }

    /**
     * @param int|null $storId
     * @return mixed
     */
    public function isChargebackEnabled($storeId = null)
    {
        return $this->getConfigValue('general/enable_chargeback', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getSendEmailOnHold($storeId = null)
    {
        return $this->getConfigValue('general/send_email', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getEmailAddressOnHold($storeId = null)
    {
        $data = $this->getConfigValue('general/email_address', $storeId);
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }

        return false;
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getEmailContentOnHold($storeId = null)
    {
        return $this->getConfigValue('general/email_content', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getEmailSubjectOnHold($storeId = null)
    {
        return $this->getConfigValue('general/email_subject', $storeId);
    }

    /**
     * Check if order can be hold
     *
     * @param mixed $ourscore
     * @param int|null $storeId
     * @return bool
     */
    public function canHold($ourscore, $storeId = null)
    {
        $scoreThreshold = $this->getConfigValue('general/score_threshold', $storeId);
        $paramsoldOrder = $this->getConfigValue('general/hold_order', $storeId);

        return $ourscore >= $scoreThreshold && $paramsoldOrder;
    }

    /**
     * Get maxmind configuration field value
     *
     * @param string $configPath
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($configPath, $storeId = null)
    {
        $sysPath = 'weltpixel_maxmind_config/' . $configPath;

        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Return Maxmind available credit
     *
     * @return mixed
     */
    public function getRemainingCredit()
    {
        $model = $this->_variableModel->loadByCode(self::MAXMIND_VARIABLE_NAME);

        $remainingCredit = $model->getPlainValue();

        return $remainingCredit;
    }


    /**
     * @param int|null $storeId
     * @return string
     */
    public function getForceIp($storeId = null)
    {
        return trim($this->getConfigValue('general/force_ip', $storeId));
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getIpExceptions($storeId = null)
    {
        return trim($this->getConfigValue('general/ip_exceptions', $storeId));
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getApiVersion($storeId = null)
    {
        return $this->getConfigValue('general/api_version', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getApiEndpoint($storeId = null)
    {
        return $this->getConfigValue('connection/api_server_endpoint', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getLicenseKey($storeId = null)
    {
        return trim($this->getConfigValue('connection/license_key', $storeId));
    }


    /**
     * @param int|null $storeId
     * @return string
     */
    public function getAccountId($storeId = null)
    {
        return trim($this->getConfigValue('connection/account_id', $storeId));
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function isDeviceTrackEnabled($storeId = null)
    {
        return $this->getConfigValue('general/enable_devicetracking', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getDeviceTrackOn($storeId = null)
    {
        return $this->getConfigValue('general/enable_devicetracking_pages', $storeId);
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getAllowedPaymentMethods($storeId = null)
    {
        $enableForPayment = $this->getConfigValue('general/enable_for_payment', $storeId);
        return explode(',', $enableForPayment);
    }

    /**
     * @param Order $order
     * @return string
     */
    public function getOrderRemoteIp($order)
    {
        $orderRemoteIp =  $order->getRemoteIp();
        if ($order->getXForwardedFor()) {
            $forwardedIps = explode(",", $order->getXForwardedFor());
            $orderRemoteIp = $forwardedIps[0];
        }

        return $orderRemoteIp;
    }
}
