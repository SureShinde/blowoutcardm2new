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
namespace Aheadworks\Acr\Model\Hydrator;

use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class CartHistory
 * @package Aheadworks\Acr\Model
 */
class Quote implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        return $entity->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        $entity->setData(array_merge($entity->getData(), $data));
        return $entity;
    }
}
