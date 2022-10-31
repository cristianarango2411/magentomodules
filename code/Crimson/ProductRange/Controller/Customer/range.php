<?php

namespace Crimson\ProductRange\Controller\Customer;
use Magento\Framework\Controller\ResultFactory;

class Range extends \Magento\Framework\App\Action\Action 
{
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context);
    }

    public function execute() 
    {   
        $params=$this->getRequest()->getParams();
        if(array_key_exists('lowRange',$params) && array_key_exists('highRange',$params) && array_key_exists('sortByPrice',$params))
        {
            $lowRange = number_format((float)$params['lowRange'], 2, '.', '');
            $highRange = number_format((float)$params['highRange'], 2, '.', '');
            $sortByPrice = $params['sortByPrice']=='1'?'asc':'desc';

            $collection=$this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $productCollection=$collection->clear()
            ->addAttributeToFilter( 'price' , array('gt' => $lowRange, 'lt' => $highRange) )
            ->setOrder('price', $sortByPrice )
            ->setPageSize(10)
            ->load();
            
            foreach ($productCollection as $product) {
                $productsArray[] = $product->getData(); 
            }
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $response->setHeader('Content-type', 'text/plain');
            $response->setContents(
                json_encode($productsArray)
            );
            return $response; 
        }
    }


    public function getProductCollection($pageSize)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $pageSize=$pageSize?$pageSize:5;
        return $collection->clear()
        ->setPageSize($pageSize)
        ->load();
    }
}