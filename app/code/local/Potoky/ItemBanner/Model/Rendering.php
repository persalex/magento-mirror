<?php

class Potoky_ItemBanner_Model_Rendering extends Mage_Core_Model_Abstract
{
    /**
     * Prepare option array for rendering_type system configuration field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('itembanner')->__('prior banner occupies the position')),
            array('value'=>2, 'label'=>Mage::helper('itembanner')->__('occupy next position if the current one is occupied'))
        );
    }
}