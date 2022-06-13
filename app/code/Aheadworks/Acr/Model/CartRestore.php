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
namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\Data\CartRestoreExtensionInterface;
use Aheadworks\Acr\Model\ResourceModel\CartRestore as CartRestoreResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CartRestore
 * @package Aheadworks\Acr\Model
 */
class CartRestore extends AbstractModel implements CartRestoreInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CartRestoreResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($cartRestoreId)
    {
        return $this->setData(self::ENTITY_ID, $cartRestoreId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventHistoryId()
    {
        return $this->getData(self::EVENT_HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventHistoryId($referenceId)
    {
        return $this->setData(self::EVENT_HISTORY_ID, $referenceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRestoreCode()
    {
        return $this->getData(self::RESTORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRestoreCode($restoreCode)
    {
        return $this->setData(self::RESTORE_CODE, $restoreCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CartRestoreExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
