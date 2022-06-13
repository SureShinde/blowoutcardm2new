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
namespace Aheadworks\Acr\Model\ResourceModel\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Magento\Framework\EntityManager\Hydrator as EntityManagerHydrator;

class Hydrator extends EntityManagerHydrator
{
    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $data = parent::extract($entity);
        if (isset($data[RuleInterface::PRODUCT_TYPE_IDS]) && is_array($data[RuleInterface::PRODUCT_TYPE_IDS])) {
            $data[RuleInterface::PRODUCT_TYPE_IDS] = implode(',', $data[RuleInterface::PRODUCT_TYPE_IDS]);
        }
        if (isset($data[RuleInterface::CUSTOMER_GROUPS]) && is_array($data[RuleInterface::CUSTOMER_GROUPS])) {
            $data[RuleInterface::CUSTOMER_GROUPS] = implode(',', $data[RuleInterface::CUSTOMER_GROUPS]);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        /** @var RuleInterface $entity */
        $entity = parent::hydrate($entity, $data);
        if (!is_array($entity->getProductTypeIds())) {
            $entity->setProductTypeIds(explode(',', $entity->getProductTypeIds()));
        }
        if (!is_array($entity->getCustomerGroups())) {
            $entity->setCustomerGroups(explode(',', $entity->getCustomerGroups()));
        }

        return $entity;
    }
}
