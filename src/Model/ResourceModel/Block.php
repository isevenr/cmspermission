<?php
namespace Isevenr\CmsPermission\Model\ResourceModel;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Isevenr CMS block model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Block extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_block_customer_group', 'block_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(BlockInterface::class)->getEntityConnection();
    }

    /**
     * Get customer group ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupCustomerGroupIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $linkField = $entityMetadata->getLinkField();
        // force linkField as block_id because Magento Commerce linkField is row_id
        $linkField = 'block_id';

        $select = $connection->select()
            ->from(['cbcg' => $this->getTable('cms_block_customer_group')], 'customer_group_id')
            ->join(
                ['cb' => $this->getTable('cms_block')],
                'cbcg.' . $linkField . ' = cb.' . $linkField,
                []
            )
            ->where('cb.' . $entityMetadata->getIdentifierField() . ' = :block_id');

        return $connection->fetchCol($select, ['block_id' => (int)$id]);
    }

    /**
     * retrieve list of cms block allowed for given $customer_group_id
     *
     * @param int $customer_group_id
     * @return array
     */
    public function lookupCmsPermissionIds($customer_group_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select('block_id')
            ->from(['cbcg' => $this->getTable('cms_block_customer_group')], 'block_id')
            ->where('cbcg.customer_group_id = :customer_group_id');

        return $connection->fetchCol($select, ['customer_group_id' => (int)$customer_group_id]);
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
