<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('alertanonymous/price');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('alert_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Product alert price id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Customer id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product id')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Price amount')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Product alert add date')
    ->addColumn('last_send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Product alert last send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product alert send count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product alert status')
    ->addIndex($installer->getIdxName('alertanonymous/price', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('alertanonymous/price', array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName('alertanonymous/price', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('alertanonymous/price', 'customer_id', 'anonymouscustomer/anonymous', 'anonymous_id'),
        'customer_id', $installer->getTable('anonymouscustomer/anonymous'), 'anonymous_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('alertanonymous/price', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('alertanonymous/price', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Product Alert Price Anonymous Customer');
$installer->getConnection()->createTable($table);

$tabName = $installer->getTable('alertanonymous/stock');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('alert_stock_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Product alert stock id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Customer id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Product alert add date')
    ->addColumn('send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Product alert send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Send Count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product alert status')
    ->addIndex($installer->getIdxName('alertanonymous/stock', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('alertanonymous/stock', array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName('alertanonymous/stock', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('alertanonymous/stock', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('alertanonymous/stock', 'customer_id', 'anonymouscustomer/anonymous', 'anonymous_id'),
        'customer_id', $installer->getTable('anonymouscustomer/anonymous'), 'anonymous_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('alertanonymous/stock', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Product Alert Stock Anonymous Customer');
$installer->getConnection()->createTable($table);
$installer->endSetup();
