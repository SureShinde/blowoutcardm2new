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
namespace Aheadworks\Acr\Controller\Cart;

use Aheadworks\Acr\Model\CartRestorer;
use Magento\Framework\App\Action\Context;
use Aheadworks\Acr\Model\CookieManagement;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Restore
 * @package Aheadworks\Acr\Controller\Cart
 */
class Restore extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CartRestorer
     */
    private $cartRestorer;

    /**
     * @var CookieManagement
     */
    private $cookieManagement;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param CartRestorer $cartRestorer
     * @param CookieManagement $cookieManagement
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CartRestorer $cartRestorer,
        CookieManagement $cookieManagement,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->cartRestorer = $cartRestorer;
        $this->cookieManagement = $cookieManagement;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                $customerId = $this->customerSession->getCustomerId();
                if ($this->cartRestorer->restore($code, $customerId)) {
                    $this->cookieManagement->invalidateTopCart();
                    return $resultRedirect->setPath('checkout/cart');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('checkout/cart');
            }
        }
        $this->messageManager->addErrorMessage(__('Wrong restore code specified'));
        return $resultRedirect->setPath('/');
    }
}
