<?php

class Potoky_ItemBanner_Model_File_Validator_Image extends Mage_Core_Model_File_Validator_Image
{
    /**
     * Validation callback for checking is file is image and whether its dimensions are greater than 800 px each one
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return null
     * @throws Mage_Core_Exception
     */
    public function validate($filePath)
    {
        $image = new Varien_Image($filePath);

        if ($image->getOriginalWidth() < 800 || $image->getOriginalHeight() < 800) {
            Mage::throwException(
                Mage::helper('itembanner')->getErrorMessage('image')[1],
                'adminhtml/session'
            );
        }

        return parent::validate($filePath);
    }
}