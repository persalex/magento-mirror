<?php

class Potoky_AnonymousCustomer_Helper_Entity extends Mage_Core_Helper_Abstract
{
    /**
     * Returns Customer Entity defined by $modelType that has given $email and $websiteId
     *
     * @param $modelType
     * @param $email
     * @param $websiteId
     * @return mixed
     */
    public function getCustomerEntityByRequest($modelType, $email, $websiteId)
    {
        $customerEntity = Mage::getModel($modelType)
            ->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('website_id', $websiteId)
            ->getFirstItem();

        return $customerEntity;
    }
}