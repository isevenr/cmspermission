<?php
namespace Isevenr\CmsPermission\Model\ResourceModel\Block\Relation\CustomerGroup;

use Isevenr\CmsPermission\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Block
     */
    protected $resourceBlock;

    /**
     * @param Block $resourceBlock
     */
    public function __construct(
        Block $resourceBlock
    ) {
        $this->resourceBlock = $resourceBlock;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $groups = $this->resourceBlock->lookupCustomerGroupIds((int)$entity->getId());
            $entity->setData('customer_group_ids', $groups);
        }
        return $entity;
    }
}
