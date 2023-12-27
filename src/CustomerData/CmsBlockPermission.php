<?php
namespace Isevenr\CmsPermission\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Cms\Api\BlockRepositoryInterface;

class CmsPermissionPermission extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Isevenr\CmsPermission\Model\ResourceModel\Block
     */
    protected $resourceBlock;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filter;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Isevenr\CmsPermission\Model\ResourceModel\Block $resourceBlock
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param \Magento\Cms\Model\Template\FilterProvider $filter
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Isevenr\CmsPermission\Model\ResourceModel\Block $resourceBlock,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filter,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = []
    ) {
        parent::__construct($data);
        $this->customerSession = $customerSession;
        $this->resourceBlock = $resourceBlock;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->blockRepository = $blockRepository;
        $this->filter = $filter;
        $this->layout = $layout;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        // if customer not logged in, force $customer_group_id to \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID
        $customer_group_id = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        if ($this->customerSession->getCustomer()->getId()) {
            $customer_group_id = $this->customerSession->getCustomer()->getGroupId();
        }

        return [
            'allowed_cms_blocks' => $this->getAllowedCmsPermissions($customer_group_id),
            'customer_group_id' => $customer_group_id
        ];
    }

    /**
     * retrieve customer 
     *
     * @param integer $customer_group_id
     *
     * @return array
     */
    public function getAllowedCmsPermissions($customer_group_id)
    {
        $block_ids = $this->resourceBlock->lookupCmsPermissionIds($customer_group_id);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('block_id', $block_ids, 'in')->create();
        $searchResults = $this->blockRepository->getList($searchCriteria);

        $result = array();
        foreach ($searchResults->getItems() as $block) {
            $result[$block->getId()] = $this->filter->getBlockFilter()->filter($block->getContent());
        }

        return $result;
    }
}
