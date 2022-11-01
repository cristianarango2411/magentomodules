<?php

namespace Crimson\ProductRange\Controller\Customer;
use Magento\Framework\Controller\ResultFactory;

class Range extends \Magento\Framework\App\Action\Action 
{
    protected $_productCollectionFactory;
    protected $_storeManager;
    protected $_productVisibility;
    protected $_productStatus;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager =  $storemanager;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
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
            $sortByPrice = $params['sortByPrice']=='1 Ascending'?'ASC':'DESC';

            if($this->valid($lowRange,$highRange)){
                $productCollection = $this->getProductCollection($lowRange, $highRange, $sortByPrice);
            
                $response=$this->getJsontoResponse($productCollection,$store);
                
                return $response; 
            }
        }
    }

    private function valid($lowRange,$highRange){
        if($lowRange<0 || $highRange<$lowRange || $highRange>($lowRange*5))
            return false;
        else
            return true;
    }

    private function getProductCollection($lowRange, $highRange, $sortByPrice)
    {
        $collection=$this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', ['in' => $this->_productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
        $collection->clear()
        ->addPriceDataFieldFilter('%s >= %s', ['final_price', $lowRange])
        ->addPriceDataFieldFilter('%s <= %s', ['final_price', $highRange])
        ->addFinalPrice();
        $sort='.final_price '.$sortByPrice;
        $collection->getSelect()->order( $sort )->limit(10);
        
        return $collection;
    }

    private function getJsontoResponse($productCollection, $store)
    {
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