<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Partnerlocations
 * @author    Bakeway
 */

namespace Bakeway\Partnerlocations\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Bakeway Partnerlocations Model.

 */
class Partnerlocations extends AbstractModel implements IdentityInterface
{
    /**
     * Bakeway Partnerlocations cache tag.
     */
    const CACHE_TAG = 'bakeway_partnerlocations';

    /**
     * @var string
     */
    protected $_cacheTag = 'bakeway_partnerlocations';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bakeway_partnerlocations';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Bakeway\Partnerlocations\Model\ResourceModel\Partnerlocations');
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
}
