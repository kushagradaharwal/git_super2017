<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PayoutsCalculation
 * @author    Bakeway
 */

namespace Bakeway\PayoutsCalculation\Model;

use Magento\Framework\Model\AbstractModel;
use Bakeway\PayoutsCalculation\Api\Data\BakewaytransactionInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Bakeway Payouts Transaction Data Model.
 *
 * @method \Bakeway\PayoutsCalculation\Model\ResourceModel\Bakewaytransaction _getResource()
 * @method \Bakeway\PayoutsCalculation\Model\ResourceModel\Bakewaytransaction getResource()
 */
class Bakewaytransaction extends AbstractModel implements BakewaytransactionInterface, IdentityInterface
{
    /**
     * Marketplace Seller cache tag.
     */
    const CACHE_TAG = 'bakeway_payouts_transaction';

    /**
     * @var string
     */
    protected $_cacheTag = 'bakeway_payouts_transaction';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bakeway_payouts_transaction';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Bakeway\PayoutsCalculation\Model\ResourceModel\Bakewaytransaction');
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSeller();
        }

        return parent::load($id, $field);
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Bakeway\PayoutsCalculation\Api\Data\BakewaytransactionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
