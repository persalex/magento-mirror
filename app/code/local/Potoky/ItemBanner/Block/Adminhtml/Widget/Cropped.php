<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Cropped extends Mage_Adminhtml_Block_Template
{
    /**
     * Original image url
     *
     * @var string
     */
    private $imageUrl;

    /**
     * Original image url
     *
     * @var int
     */
    private $imageSquare;

    /**
     * Parent construct plus setting $this properties plus setting template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->imageUrl = $this->prepareImageUrl();
        $this->imageSquare = $this->measureImageSquare();
        if (Mage::registry('current_widget_instance')->getWidgetParameters()['image']) {
            $this->setTemplate('itembanner/cropped.phtml');
        } else {
            $this->setTemplate('');
        }
    }

    /**
     * Prepare original image url
     *
     * @return mixed
     */
    private function prepareImageUrl() {
        return Mage::helper('itembanner')->getImageUri(
            Mage::registry('current_widget_instance')->getWidgetParameters()['image']
        );
    }

    /**
     * Measure original image square
     *
     * @return mixed
     */
    private function measureImageSquare()
    {
        $imageData = getimagesize($this->imageUrl);

        return $imageData[0] * $imageData[1];
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getImageSquare()
    {
        return $this->imageSquare;
    }
}