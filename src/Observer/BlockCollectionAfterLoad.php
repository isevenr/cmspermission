<?php

namespace Isevenr\CmsPermission\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Isevenr\CmsPermission\Model\ResourceModel\Block;

class BlockCollectionAfterLoad implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Block
     */
    protected $resourceBlock;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Block $resourceBlock
    ) {
        $this->_objectManager = $objectManager;
        $this->resourceBlock = $resourceBlock;
    }

    /**
     * trigger after product save in admin
     * @return void
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getBlockCollection();
        // only load customer_group_ids when collection size is 1 - which means loading single cms block
        if ($collection->getSize() == 1) {
            foreach ($collection as $block){
                $groups = $this->resourceBlock->lookupCustomerGroupIds((int)$block->getBlockId());
                $block->setData('customer_group_ids', $groups);
            }
        }
    }
}
