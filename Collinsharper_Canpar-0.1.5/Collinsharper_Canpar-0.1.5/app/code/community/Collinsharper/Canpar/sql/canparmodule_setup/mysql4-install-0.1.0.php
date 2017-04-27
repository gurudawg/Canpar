<?php
/**
 * Created by Gayan Hewa
 * User: Gayan
 * Date: 9/20/13
 * Time: 4:14 AM
 */
/* @var  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('canparmodule/shipment')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` varchar(256) DEFAULT NULL,
  `magento_shipment_id` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `tracking_code` varchar(256) DEFAULT NULL,
  `manifest_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `{$this->getTable('canparmodule/manifest')}` (
  `manifest_id` int(11) NOT NULL AUTO_INCREMENT,
  `canpar_manifest_num` varchar(256) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`manifest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


alter table `{$this->getTable('sales/order')}` add column canpar_shipping_options varchar(255) comment 'Shipping options for canpar shipment';
";



$installer->run($sql);
$installer->endSetup();