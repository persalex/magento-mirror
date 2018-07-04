<?php

class Potoky_ViewedProducts_Model_Event_Observer extends Mage_Reports_Model_Event_Observer
{
    /**
     * Customer login action. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        Mage::helper('viewedproducts/session')->processCookieForViewedProducts('reset');

        return parent::customerLogin($observer);
    }

    /**
     * Customer logout processing. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        Mage::helper('viewedproducts/session')->processCookieForViewedProducts('clear');

        return parent::customerLogout($observer);
    }

    /**
     * View Catalog Product action. Ads cookie that prevents
     * JS on the Product page from sending a Product updating AJAX.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function catalogProductView(Varien_Event_Observer $observer)
    {
        Mage::helper('viewedproducts/session')->processCookieForViewedProducts('updated_');

        return parent::catalogProductView($observer);
    }
}
