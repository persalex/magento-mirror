<?php

class Potoky_ViewedProducts_Helper_Session extends Mage_Core_Helper_Abstract
{
    /**
     * Creates a cookie that should make the JS Block
     * clear its data or reload it from the server and
     * ads template with script to process it.
     *
     * @param string $type
     * @return void
     */
    public function processCookieForViewedProducts($type)
    {
        if ($type === 'updated_' && ($cookie = Mage::getSingleton('core/cookie')->get('viewed_products'))) {
            $type = $type . $cookie;
        }
        Mage::getSingleton('core/cookie')->set('viewed_products', $type, 0, '/', null, null, false);
    }
}

