<?php

namespace Vimmi\Conditions\Plugin\SalesRule\Conditions;

/**
 * Additional attr for validator.
 */
class Combine
{
    /**
     * Add additional condition
     * @return array
     */
    public function afterGetNewChildSelectOptions($subject, $result) {

        $result[4]['value'][] = $this->getCustomerFirstOrderCondition();
        
        return $result;
    }

    /**
     * Prepare additional condition
     * @return array
     */
    private function getCustomerFirstOrderCondition()
    {
        return [
            'label'=> __('The cart has an item with red color'),
            'value'=> \Vimmi\Conditions\Model\Rule\Condition\Color::class
        ];
    }
}