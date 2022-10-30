<?php
namespace Crimson\ProductRange\Block;
class ProductRangeTab extends \Magento\Framework\View\Element\Template
{    
  
    protected $_productCollectionFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,        
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context, $data);
    }
    
    /*public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setPageSize(3); // fetching only 3 products
        return $collection;
    }*/
    
    public function getProductCollection($pageSize)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $pageSize=$pageSize?$pageSize:5;
        return $collection->clear()
        ->setPageSize($pageSize)
        ->load();
    }
    
    public function getProductCollectionByCategories($ids)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => ids]);
        return $collection;
    }
}