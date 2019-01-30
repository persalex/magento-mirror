<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('anonymouscustomer/anonymous');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('anonymous_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Anonymous Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Website Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Email')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Group Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
    ), 'Store Id')
    ->addColumn('created_at',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'false',
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Created At')
    ->addColumn('registered_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'true',
        'default' => null
    ), 'Time when this anonymous customer was registered')
    ->addColumn('registration_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Registration Id for a customer with these website_id and email when such one is registered')
    ->addIndex($installer->getIdxName('anonymouscustomer/anonymous', array('registration_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('registration_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('anonymouscustomer/anonymous', array('email', 'website_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('email', 'website_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));
$installer->getConnection()->createTable($table);
$installer->endSetup();
