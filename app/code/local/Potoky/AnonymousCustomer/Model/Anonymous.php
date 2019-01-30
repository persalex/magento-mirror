<?php

class Potoky_AnonymousCustomer_Model_Anonymous extends Mage_Core_Model_Abstract
{
    /**
     * When set to true it will not allow to create an anonymous customer if
     * the appropriate regular customer (with the same email and website) id already exists
     *
     * @var bool
     */
    private $checkIfRegistered = true;

    protected function _construct()
    {
        $this->_init('anonymouscustomer/anonymous');
    }

    public function setCheckIfRegistered($bool)
    {
        $this->checkIfRegistered = $bool;
    }

    public function getCheckIfRegistered()
    {
        return $this->checkIfRegistered;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $parent = parent::_beforeSave();
        if ($this->isObjectNew() && $this->checkIfRegistered) {
            $customer = Mage::helper('anonymouscustomer/entity')->getCustomerEntityByRequest(
                'customer/customer',
                $this->getData('email'),
                $this->getData('website_id')
            );
            if ($customer->getId()) {
                Mage::throwException('A regular Customer with such email and website already exists');
            }
        }

        return $parent;
    }
}
