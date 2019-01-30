<?php

class Potoky_AlertAnonymous_Block_Email_Price extends Mage_ProductAlert_Block_Email_Price
{
    /**
     * Hashed unsubscribe information
     *
     * @var string
     */
    private $unsubscribeInfo = 'noinfo';

    /**
     * Sets hashed unsubscribe info
     *
     * @var $hash
     */
    public function setUnsubscribeInfo($info)
    {
        $this->unsubscribeInfo = $info;
    }

    /**
     * Get store url params after adding to them hashed unsubscribe info
     *
     * @return string
     */
    protected function _getUrlParams()
    {
        $parent = parent::_getUrlParams();
        $parent['unsubscribe'] = $this->unsubscribeInfo;

        return $parent;
    }
}