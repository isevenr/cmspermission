<?php
namespace Isevenr\CmsPermission\Model\ResourceModel\Block\Relation\CustomerGroup;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Isevenr\CmsPermission\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Block
     */
    protected $resourceBlock;

    /**
     * @param MetadataPool $metadataPool
     * @param Block $resourceBlock
     */
    public function __construct(
        MetadataPool $metadataPool,
        Block $resourceBlock
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBlock = $resourceBlock;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $linkField = $entityMetadata->getLinkField();
        // force linkField as block_id because Magento Commerce linkField is row_id
        $linkField = 'block_id';

        $connection = $entityMetadata->getEntityConnection();

        $oldGroups = $this->resourceBlock->lookupCustomerGroupIds((int)$entity->getId());
        $newGroups = (array)$entity->getData('customer_group_ids');

        $table = $this->resourceBlock->getTable('cms_block_customer_group');

        $delete = array_diff($oldGroups, $newGroups);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'customer_group_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newGroups, $oldGroups);
        if ($insert) {
            $data = [];
            foreach ($insert as $customerGroupId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
