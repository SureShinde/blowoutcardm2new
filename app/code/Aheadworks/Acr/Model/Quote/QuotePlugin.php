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
namespace Aheadworks\Acr\Model\Quote;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Magento\Quote\Model\Quote;

class QuotePlugin
{
    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CartHistoryManagementInterface $cartHistoryManagement
     * @param Config $config
     */
    public function __construct(
        CartHistoryManagementInterface $cartHistoryManagement,
        Config $config
    ) {
        $this->cartHistoryManagement = $cartHistoryManagement;
        $this->config = $config;
    }

    /**
     * Add quote to cart history
     *
     * @param Quote $subject
     * @param Quote $result
     * @return Quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAfterSave(
        Quote $subject,
        Quote $result
    ) {
        if ($this->config->isEnabled()
            && $result->getCustomerEmail()
        ) {
            $cartData = array_merge($result->getData(), [
                'email' => $result->getCustomerEmail(),
                'customer_name' => $result->getCustomerFirstname() . ' ' . $result->getCustomerLastname()
            ]);
            $this->cartHistoryManagement->addCartToCartHistory($cartData);
        }
        return $result;
    }
}
