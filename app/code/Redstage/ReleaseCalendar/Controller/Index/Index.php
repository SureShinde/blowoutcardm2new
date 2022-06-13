<?php
namespace Redstage\ReleaseCalendar\Controller\Index;

use Redstage\ReleaseCalendar\Block\Product\ReleaseProductList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Redstage\ReleaseCalendar\Setup\Patch\Data\ProductReleaseDateAttribute;

class Index extends Action
{
    /** @var PageFactory */
    protected $pageFactory;

    /** @var  \Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $productCollection;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        TimezoneInterface $timezone
    )
    {
        $this->pageFactory = $pageFactory;
        $this->productCollection = $collectionFactory->create();
        $this->timezone = $timezone;
        
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();

        // obtain product collection.
        $today = $this->timezone->date()->format('Y-m-d');
        $this->productCollection->addAttributeToSelect('*');
        
        $this->productCollection->addAttributeToFilter(
            'releasedate',
            ['gt' => $today]
        );

        /* ProductReleaseDateAttribute::RELEASE_DATE_ATTR_CODE */
        
        $this->productCollection->setOrder(
            'releasedate',
            SortOrder::SORT_ASC
        );
        
        // get the custom list block and add our collection to it
        /** @var ReleaseProductList $list */
        $list = $result->getLayout()->getBlock('release.products.list');
        $list->setProductCollection($this->productCollection);

        return $result;
    }
}