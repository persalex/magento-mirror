<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * All the blocks of this type initiated in layout
     *
     * @var array
     */
    public static $allOfTheType = [];

    /**
     * In addition to parent method replenish $this allOfTheType with newly got name in layout
     *
     * @param string $name
     * @return Mage_Core_Block_Abstract
     */
    public function setNameInLayout($name)
    {
        self::$allOfTheType[] = $name;

        return parent::setNameInLayout($name);
    }

    /**
     * Return this block's banner image url
     *
     * @return mixed
     */
    public function getImageUrl()
    {
        return Mage::helper('itembanner')->getImageUri(
            $this->getData('image'),
            Mage::registry('potoky_itembanner')['mode'] ?? ''
        );
    }
}