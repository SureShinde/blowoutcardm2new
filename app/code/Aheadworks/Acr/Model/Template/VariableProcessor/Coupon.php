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
namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Aheadworks\Acr\Api\CouponVariableManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Coupon
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Coupon implements VariableProcessorInterface
{
    /**
     * @var CouponVariableManagementInterface
     */
    private $couponVariableManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CouponVariableManagementInterface $couponVariableManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CouponVariableManagementInterface $couponVariableManager,
        StoreManagerInterface $storeManager
    ) {
        $this->couponVariableManager = $couponVariableManager;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::COUPON => $this->couponVariableManager->getCouponVariable(
            $params['rule_id'],
            $params['store_id']
        )];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::COUPON => $this->couponVariableManager->getTestCouponVariable()];
    }
}
