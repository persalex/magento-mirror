<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    const ALERT_SAVE_SUCCESS_MESSAGE_PRICE = 'The alert subscription has been saved.';
    const ALERT_SAVE_FAILURE_MESSAGE_PRICE = 'Unable to update the alert subscription.';
    const ALERT_SAVE_SUCCESS_MESSAGE_STOCK = 'Alert subscription has been saved.';
    const ALERT_SAVE_FAILURE_MESSAGE_STOCK = 'Unable to update the alert subscription.';

    public static $helpers = [];

    private $alertTypes = ['price', 'stock'];

    private $rewriteMessage;

    public function __construct(){
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    /**
     * Run process send product alerts
     *
     * @return Mage_ProductAlert_Model_Observer
     */
    public function process()
    {
        $parent = parent::process();
        self::$helpers['registry']->setRegistry('cron', null, false);
        parent::process();
        self::$helpers['registry']->setRegistry();

        return $parent;
    }

    /**
     * Avoid success/failure message appearance when other needed message
     * is set to appear and avoid saving anonymous alerts
     * when the corresponding regular alerts already exist
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function avoidUnneededActions(Varien_Event_Observer $observer)
    {
        $alert = $observer->getEvent()->getObject();
        if (self::$helpers['registry']->getRegistry('context') == 'add') {
            $data = $this->extractAlertRelatedData(clone $alert);
            if($data['status'] === "0") {
                if($data['alert_instance'] == 'stock') {
                    $this->rewriteMessage = self::$helpers['data']->__('You are already subscribed for this Stock alert.');
                }
                elseif ($alert->getPrice() == $data['price']) {
                    $this->rewriteMessage = self::$helpers['data']->__('You are already subscribed for this Price alert.');
                }
            }
        }

        $anonymousCustomer = Mage::getModel('anonymouscustomer/anonymous')->load($alert->getCustomerId());
        if($anonymousCustomer && $anonymousCustomer->getRegistrationId()) {
            $alert->_setDataSaveAllowed(false);
        }

        return $this;
    }

    /**
     * Extract Alert concerning data
     *
     * @var Mage_ProductAlert_Model_Price | Mage_ProductAlert_Model_Stock $alert
     * @return array $data
     */
    private function extractAlertRelatedData($alert)
    {
        $data = [];

        if (!$alert->getId()) {
            $alert->loadByParam();
        }

        if ($alert instanceof Mage_ProductAlert_Model_Price) {
            $data['price'] = $alert->getData('price');
            $data['alert_instance'] = 'price';
        } elseif ($alert instanceof Mage_ProductAlert_Model_Stock) {
            $data['alert_instance'] = 'stock';
        }
        $data['customer_id'] = $alert->getCustomerId();
        $data['website_id'] = $alert->getWebsiteId();
        $data['product_id'] = $alert->getProductId();
        $data['email'] = self::$helpers['registry']->getRegistry('customer_entity')->getEmail();
        $data['status'] = $alert->getData('status');

        return $data;
    }

    /**
     * Riwrite success/failire core module message with $this->>rewriteMessage if set
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function rewriteMessage(Varien_Event_Observer $observer)
    {
        if (isset($this->rewriteMessage)) {
            $messages = Mage::getSingleton('catalog/session')->getMessages();
            $lastAddedMessage =$messages->getLastAddedMessage();
            $code = $lastAddedMessage->getCode();
            if($code == self::$helpers['data_1']->__(self::ALERT_SAVE_SUCCESS_MESSAGE_PRICE) ||
                $code == self::$helpers['data_1']->__(self::ALERT_SAVE_FAILURE_MESSAGE_PRICE) ||
                $code == self::$helpers['data_1']->__(self::ALERT_SAVE_SUCCESS_MESSAGE_STOCK) ||
                $code == self::$helpers['data_1']->__(self::ALERT_SAVE_FAILURE_MESSAGE_STOCK)) {
                $lastAddedMessage->setCode($this->rewriteMessage);
            }
        }

        return $this;
    }

    /**
     * Aunomatically delete Anonymous Customer price alert when
     * corresponding Regular Customer price alert is deleted
     *
     * @param Varien_Event_Observer $observer
     */
    public function cascadeDeletePrice(Varien_Event_Observer $observer)
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            return;
        }

        $alert = $observer->getEvent()->getObject();
        $this->processDelete(
            $this->extractAlertRelatedData($alert),
            'price'
        );
    }

    /**
     * Aunomatically delete all Anonymous Customer price alerts when
     * corresponding Regular Customer price alerts are deleted
     *
     * @param Varien_Event_Observer $observer
     */
    public function cascadeDeletePriceAll(Varien_Event_Observer $observer)
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            return;
        }

        $data = [];
        $customerId = self::$helpers['registry']->getRegistry('customer_entity')->getId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $data['email'] = $customer->getEmail();
        $data['website_id'] = $customer->getWebsiteId();
        $this->processDelete($data, 'priceAll');   
    }

    /**
     * Aunomatically delete Anonymous Customer stock alert when
     * corresponding Regular Customer stock alert is deleted
     *
     * @param Varien_Event_Observer $observer
     */
    public function cascadeDeleteStock(Varien_Event_Observer $observer)
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            return;
        }

        $alert = $observer->getEvent()->getObject();
        $this->processDelete(
            $this->extractAlertRelatedData($alert),
            'stock'
        );
    }

    /**
     * Aunomatically delete all Anonymous Customer stock alerts when
     * corresponding Regular Customer stock alerts are deleted
     *
     * @param Varien_Event_Observer $observer
     */
    public function cascadeDeleteStockAll(Varien_Event_Observer $observer)
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            return;
        }

        $data = [];
        $customerId = self::$helpers['registry']->getRegistry('customer_entity')->getId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $data['email'] = $customer->getEmail();
        $data['website_id'] = $customer->getWebsiteId();
        $this->processDelete($data, 'stockAll');
    }

    /**
     * Processes cascade deletes
     *
     * @param $data
     * @param $actionName
     * @return $this
     */
    private function processDelete($data, $actionName)
    {
        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest(
                'anonymouscustomer/anonymous',
                $data['email'],
                $data['website_id']
        );
        if ($id = $anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
            $modelName = (strstr($actionName, 'All') !== 'All') ? $actionName : strstr($actionName, 'All', true);
            if ($modelName === $actionName) {
                $anonymousAlert = $model  = Mage::getModel('productalert/' . $modelName)
                    ->setCustomerId($id)
                    ->setProductId($data['product_id'])
                    ->setWebsiteId($data['website_id'])
                    ->loadByParam();
                $anonymousAlert->delete();
            } else {
                Mage::getModel('productalert/' . $modelName)->deleteCustomer(
                    $id,
                    $data['website_id']
                );
            }
        }

        return $this;
    }

    /**
     * Copy alerts of the Anonymous Customer to Core Alert tables when
     * regular the corresponding regular customer is created
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function copyAlertsToCoreTables(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $email = $customer->getEmail();
        $websiteId = $customer->getWebsiteId();
        $anonymousCustomer = self::$helpers['entity']
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if(!$anonymousCustomer->getId()) {
            return $this;
        }
        foreach ($this->alertTypes as $alertype) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
            $collection = Mage::getModel('alertanonymous/' . $alertype)
                ->getCollection()
                ->addFieldToFilter('customer_id', $anonymousCustomer->getId())
                ->addFieldToFilter('website_id', $websiteId);
            foreach ($collection as $anonymousAlert) {
                self::$helpers['registry']->setRegistry();
                $coreAlert = Mage::getModel('productalert/' . $alertype);
                $coreAlert->setData([
                    'customer_id' => $customer->getId(),
                    'product_id'  => $anonymousAlert->getProductId(),
                    'website_id'  => $anonymousAlert->getWebsiteId(),
                    'add_date'    => $anonymousAlert->getAddDate(),
                    'send_date'   => $anonymousAlert->getSendDate(),
                    'status'      => $anonymousAlert->getStatus(),
                ]);
                if ($alertype === 'price') {
                    $coreAlert->setData('price', $anonymousAlert->getPrice());
                }
                try{
                    $coreAlert->save();
                } catch(Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        return $this;
    }
}
