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
namespace Aheadworks\Acr\Test\Unit\Model\Source;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Source\Groups;
use Magento\Framework\Convert\DataObject as ConvertDataObject;

/**
 * Class GroupsTest
 * Test for \Aheadworks\Acr\Model\Source\Groups
 *
 * @package Aheadworks\Acr\Test\Unit\Model\Source
 */
class GroupsTest extends TestCase
{
    /**
     * @var Groups
     */
    private $model;

    /**
     * @var GroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var ConvertDataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->groupRepositoryMock = $this->getMockForAbstractClass(GroupRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );
        $this->objectConverterMock = $this->createPartialMock(ConvertDataObject::class, ['toOptionArray']);

        $this->model = $objectManager->getObject(
            Groups::class,
            [
                'groupRepository' => $this->groupRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'objectConverter' => $this->objectConverterMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $groupSearchResultsMock = $this->getMockForAbstractClass(GroupSearchResultsInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('customer_group_id', GroupInterface::NOT_LOGGED_IN_ID, 'neq');
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($groupSearchResultsMock);

        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$groupMock]);

        $this->objectConverterMock->expects($this->once())
            ->method('toOptionArray')
            ->with([$groupMock], 'id', 'code')
            ->willReturn([]);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
