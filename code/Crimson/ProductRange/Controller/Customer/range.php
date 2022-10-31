<?php

namespace Crimson\ProductRange\Controller\Customer;
use Magento\Framework\Controller\ResultFactory;

class Range extends \Magento\Framework\App\Action\Action 
{
    protected $_productCollectionFactory;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storemanager
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager =  $storemanager;
        parent::__construct($context);
    }

    public function execute() 
    {   
        $params=$this->getRequest()->getParams();
        if(array_key_exists('lowRange',$params) && array_key_exists('highRange',$params) && array_key_exists('sortByPrice',$params))
        {
            $store = $this->_storeManager->getStore();
            $lowRange = number_format((float)$params['lowRange'], 2, '.', '');
            $highRange = number_format((float)$params['highRange'], 2, '.', '');
            $sortByPrice = $params['sortByPrice']=='1 Ascending'?'asc':'desc';

            $collection=$this->_productCollectionFactory->create();
            //$collection->addAttributeToSelect('*');
            $collection->addAttributeToSelect('*');
            $collection->clear()
            ->addFieldToFilter('price',array('gteq'=>$lowRange))
            ->addFieldToFilter('price',array('lteq'=>$highRange))
            ->addAttributeToSort('price', $sortByPrice );
            $collection->getSelect()->limit(10);

            $productCollection=$collection;

            foreach ($productCollection as $product) {
                $p = $product->getData();   
                $p['url'] = $product->getProductUrl();
                $p['image'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getImage();
                $p['final_price']=number_format($p['final_price'], 2, ',', ' ');
                $productsArray[] = $p;
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