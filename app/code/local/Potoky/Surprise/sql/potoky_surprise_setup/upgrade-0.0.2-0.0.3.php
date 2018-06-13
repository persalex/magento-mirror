<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$tabName = $installer->getTable('potoky_surprise/surprise');

$installer->getConnection()
    ->addColumn($tabName, 'surp_as_comm', array(
        'nullable'  => true,
        'type'=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'precision' => 12,
        'scale' => 4,
        'default' => 0,
        'comment' => 'Quantity as of common product in cart'
    ));

$installer->endSetup();