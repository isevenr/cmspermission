<?php

namespace Isevenr\CmsPermission\Plugin\Cms\Model;

use Magento\Framework\App\State;

class Block
{
    /**
     * @var State
     */
    private $state;

    /**
     * @param State $state
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }

    /**
     * @param \Magento\Cms\Model\Block $subject
     * @param $result
     */
    public function afterGetContent(\Magento\Cms\Model\Block $subject, $result)
    {
        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND) {
            // replace content of blocks with permissions
            // wrap result inside a wrapper and hide it
            if (is_array($subject->getCustomerGroupIds())
                && count($subject->getCustomerGroupIds()) > 0
                && !in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $subject->getCustomerGroupIds())) {
                $result = '<div class="cms-block-permissions" data-cms-block-id="'.$subject->getId().'" style="display:none">'.$result.'</div>';
            }
        }

        return $result;
    }
}
