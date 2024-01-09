<?php

namespace Vimmi\Conditions\Model\Rule\Condition;

/**
 * Additional attr for validator.
 */
class Color extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

     /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    public const COLOR_USED_FOR_CONDITION = 'Red';

    /**
     * Constructor
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
    ) {
        $this->request = $request;
        $this->sourceYesno = $sourceYesno;
        $this->productloader = $productloader;
        parent::__construct($context, $data);
    }

    /**
     * Load attribute options
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'is_the_cart_item_with_red_color' => __('The cart has an item with red color')
        ]);
        return $this;
    }

    /**
     * Get input type
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Get value element type
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get value select options
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData(
                'value_select_options',
                $this->sourceYesno->toOptionArray()
            );
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate if cart apply condition
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $conditionResult = 0;

        if ($object->getQuote() && $object->getQuote()->getItems()) {
            foreach ($object->getQuote()->getItems() as $item) {
                $productId = $item->getProduct()->getId();

                if ($option = $item->getOptionByCode('simple_product')) {
                    $productId = $option->getProduct()->getId();
                }
                $product = $this->productloader->create()->load($productId);
                
                if (!$conditionResult) {
                    $conditionResult = $this->loadProductColor($product);
                }
            }
        }

        $object->setData('is_the_cart_item_with_red_color', $conditionResult);
        return parent::validate($object);
    }

    /**
     * Validate if cart apply condition
     * @param \Magento\Catalog\Model\Product $product
     * @return integer
     */
    private function loadProductColor($product) {
        $attr = $product->getResource()->getAttribute('color');

        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionId(self::COLOR_USED_FOR_CONDITION) == $product->getColor() ? 1 : 0;
        }
    }
}