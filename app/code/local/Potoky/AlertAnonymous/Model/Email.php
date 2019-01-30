<?php

class Potoky_AlertAnonymous_Model_Email extends Mage_ProductAlert_Model_Email
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template_anonymous';

    public static $helpers = [];

    protected function _construct(){
        parent::_construct();
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    /**
     * Retrieve price block adding to hashed unsubscribe info
     *
     * @return Mage_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        $parent = parent::_getPriceBlock();
        $parent->setUnsubscribeInfo(rawurlencode(
            $this->_customer->getEmail() . ' ' . $this->_customer->getWebsiteId()
            ));

        return $parent;
    }

    /**
     * Retrieve price block adding to hashed unsubscribe info
     *
     * @return Mage_ProductAlert_Block_Email_Stock
     */
    protected function _getStockBlock()
    {
        $parent = parent::_getStockBlock();
        $parent->setUnsubscribeInfo(rawurlencode(
            $this->_customer->getEmail() . ' ' . $this->_customer->getWebsiteId()
        ));

        return $parent;
    }

    /**
     * Set customer by id. Set it to null if its an anonymous customer
     * that has a corresponding regular customer
     *
     * @param int $customerId
     * @return Mage_ProductAlert_Model_Email
     */
    public function setCustomerId($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getRegistrationId()) {
            $this->_customer = $customer;
        } else {
            $this->_customer = null;
        }

        return $this;
    }

    /**
     * Set customer model. Set it to null if its an anonymous customer
     * that has a corresponding regular customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_ProductAlert_Model_Email
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->getRegistrationId()) {
            $this->_customer = $customer;
        } else {
            $this->_customer = null;
        }

        return $this;
    }
}