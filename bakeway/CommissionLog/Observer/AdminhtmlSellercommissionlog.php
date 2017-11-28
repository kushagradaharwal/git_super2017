<?php
namespace Bakeway\CommissionLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;


class AdminhtmlSellercommissionlog implements ObserverInterface
{


    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;


    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager;
    protected $commissionLogFactory;


    /**
     * @var $_sellerobject
     */
    protected $_sellerobject;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    public function __construct(
        Filesystem $filesystem,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bakeway\CommissionLog\Model\CommissionLogFactory $commissionLogFactory,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Webkul\Marketplace\Model\SellerFactory $_sellerObjFactory
    )
    {
        $this->_storeManager = $storeManager;
        $this->_date = $date;
        $this->_jsonDecoder = $jsonDecoder;
        $this->commissionLogFactory = $commissionLogFactory;
        $this->auth = $context->getAuth();
        $this->_sellerobject = $_sellerObjFactory;
    }

    /**
     * admin customer save after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $customerid = $customer->getId();
        $postData = $observer->getRequest()->getPostValue();

        if ($this->isSeller($customerid)) {

            $_SellerCommModel = $this->commissionLogFactory->create();
            $_SellerCommModel->setSellerId($customerid);

            $_SellerCommModel->setCreatedAt($this->_date->gmtDate());
            $_SellerCommModel->setCreatedBy($this->getCurrentadminemail());
            $collectionselect = $this->commissionLogFactory->create()
                ->getCollection()
                ->addFieldToFilter(
                    'seller_id',
                    $customerid
                );
            /*commneted this as no need to check duplicate entry here */
            /*if ($collectionselect->getSize() == 1) {
                foreach ($collectionselect as $verifyrow) {
                    $rowid = $verifyrow->getId();
                }
                $_SellerCommModelDupli = $this->_objectManager->get('Bakeway\CommissionLog\Model\CommissionLog')->load($rowid);

                if (isset($postData['commission'])) {
                    try {
                        $_SellerCommModelDupli->setCommissionRate($postData['commission']);
                        $_SellerCommModelDupli->setCreatedBy($this->getCurrentadminemail());
                        $_SellerCommModelDupli->save();
                    } catch (\Exception $e) {
                        $this->_messageManager->addError($e->getMessage());
                    }
                }
            } else {*/
                try {
                    if(isset($postData['commission'])):
                    $_SellerCommModel->setCommissionRate($postData['commission']);
                    $_SellerCommModel->save();
                    endif;
                } catch (\Exception $e) {
                    $this->_messageManager->addError($e->getMessage());
                }

        }
    }


    public function getCurrentadminemail()
    {
        $loginUserEmail = $this->auth->getUser()->getUsername();

        if (!empty($loginUserEmail)) {
            return $loginUserEmail;
        }
        return;
    }

    public function isSeller($customerid)
    {
        $sellerStatus = 0;
        $model = $this->_sellerobject->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $customerid);
        foreach ($model as $value) {
            $sellerStatus = $value->getIsSeller();
        }

        return $sellerStatus;
    }


}
