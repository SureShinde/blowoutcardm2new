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
namespace Aheadworks\Acr\Plugin\Customer\Model;

use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Magento\Checkout\Model\Session as Session;

/**
 * Class AccountManagementInterfacePlugin
 * @package Aheadworks\Acr\Plugin\Customer\Api
 */
class AccountManagementPlugin
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagement;

    /**
     * AccountManagementInterfacePlugin constructor.
     * @param CartHistoryManagementInterface $cartHistoryManagement
     * @param Session $session
     */
    public function __construct(
        CartHistoryManagementInterface $cartHistoryManagement,
        Session $session
    ) {
        $this->cartHistoryManagement = $cartHistoryManagement;
        $this->checkoutSession = $session;
    }

    /**
     * @param \Magento\Customer\Api\AccountManagementInterface $interceptor
     * @param $customerEmail
     * @param int $websiteId
     */
    public function beforeIsEmailAvailable(
        \Magento\Customer\Api\AccountManagementInterface $interceptor,
        $customerEmail,
        $websiteId = null
    ) {
        $quote = $this->checkoutSession->getQuote();
        if ($quote) {
            if (($quote->getCustomerFirstname() && $quote->getCustomerLastname()) ||
                ($quote->getBillingAddress()->getFirstname() && $quote->getBillingAddress()->getLastname())
            ) {
                $customerName = ($quote->getCustomerFirstname() && $quote->getCustomerLastname()) ?
                    $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname() :
                    $quote->getBillingAddress()->getFirstname() . ' ' . $quote->getBillingAddress()->getLastname();
            } else {
                $customerName = \Aheadworks\Acr\Model\Template\VariableProcessor\Customer::DEFAULT_CUSTOMER_NAME;
            }
            $cartData = array_merge($quote->getData(), [
                'email' => $customerEmail,
                'customer_name' => $customerName
            ]);
            if ($quote->hasItems()) {
                $this->cartHistoryManagement->addCartToCartHistory($cartData);
            }
        }
    }
}
