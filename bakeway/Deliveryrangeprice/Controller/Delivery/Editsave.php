<?php
namespace Bakeway\Deliveryrangeprice\Controller\Delivery;

class Editsave extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $rangepriceFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->rangepriceFactory = $rangepriceFactory;
        $this->coreRegistry = $coreRegistry;

    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $_Responce = array();
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->getConfig()->getTitle()->set('Delivery Range & Price');
        
        $_id = $this->getRequest()->getParam('delivery_id');
        $_fieldid = $this->getRequest()->getParam('field_id');
        $_status = $this->getRequest()->getParam('is_active');
        $_fromkms = $this->getRequest()->getParam('from_kms');
        $_tokms  = $this->getRequest()->getParam('to_kms');
        $_deliveryprice = $this->getRequest()->getParam('delivery_price');
		$_updateddate = $this->getRequest()->getParam('updated_time');

        if($_status == "Enable"):
        $_status  = 1; 
        else:
        $_status  = 0; 
        endif;


        if(!empty($_fromkms) ||  !empty($_tokms) || !empty( $_deliveryprice)):
        $_Model =  $this->rangepriceFactory->create()->load($_id);
       // $_Model->setSellerId($_sellerid );
        $_Model->setFieldId($_fieldid );
        $_Model->setIsActive($_status);
        $_Model->setFromKms($_fromkms);
        $_Model->setToKms($_tokms);
        $_Model->setDeliveryPrice($_deliveryprice);
		$_Model->setUpdatedAt($_updateddate);
        try {
              $_Model->save();
             $_Responce['savedsuccess'] = 'Delivery Charges has been saved.';

        
        }catch( \Exception $e){

             $_Responce['savederror']  = 'Something went wrong while saving.';
        }
		 endif;
       die;
    }
}
