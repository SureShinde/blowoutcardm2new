<?php
namespace Meetanshi\AdvanceContact\Model\AdvanceContact;

use Meetanshi\AdvanceContact\Model\ResourceModel\AdvanceContact\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $_loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $CollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $CollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $department) {
            $this->_loadedData[$department->getId()] = $department->getData();
        }
        return $this->_loadedData;
    }
}
