<?php

class Potoky_AlertAnonymous_Block_Product_View extends Mage_ProductAlert_Block_Product_View
{
    public static $helpers = [];

    /**
     * The id (if set) of the self template highest level DOM element
     *
     * @var string
     */
    private $templateId = null;

    protected function _construct(){
        parent::_construct();
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    /**
     * Gets Id to the self template
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Check whether the stock alert data can be shown and prepare related data
     * adding related template id before
     *
     * @return void
     */
    public function prepareStockAlertData()
    {
        $this->templateId = 'stock';

        parent::prepareStockAlertData();
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     * adding related template id before
     *
     * @return void
     */
    public function preparePriceAlertData()
    {
        $this->templateId = 'price';

        parent::preparePriceAlertData();
    }
}