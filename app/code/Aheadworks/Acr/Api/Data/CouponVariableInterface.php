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
namespace Aheadworks\Acr\Api\Data;

/**
 * Interface CouponVariableInterface
 * @api
 */
interface CouponVariableInterface
{
    /**#@+
     * Constants defined for keys of the data array
     * Identical to the name of the getter in snake case
     */
    const CODE               = 'code';
    const EXPIRATION_DATE    = 'expiration_date';
    const DISCOUNT           = 'discount';
    const USES_PER_COUPON    = 'uses_per_coupon';
    /**#@-*/

    /**
     * Get coupon code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set coupon code
     *
     * @param string $couponCode
     * @return $this
     */
    public function setCode($couponCode);

    /**
     * Get coupon expiration date
     *
     * @return string
     */
    public function getExpirationDate();

    /**
     * Set coupon expiration date
     *
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate);

    /**
     * Get coupon discount
     *
     * @return string
     */
    public function getDiscount();

    /**
     * Set coupon discount
     *
     * @param string $couponDiscount
     * @return $this
     */
    public function setDiscount($couponDiscount);

    /**
     * Get uses per coupon
     *
     * @return string
     */
    public function getUsesPerCoupon();

    /**
     * Set uses per coupon
     *
     * @param string $usesPerCoupon
     * @return $this
     */
    public function setUsesPerCoupon($usesPerCoupon);
}
