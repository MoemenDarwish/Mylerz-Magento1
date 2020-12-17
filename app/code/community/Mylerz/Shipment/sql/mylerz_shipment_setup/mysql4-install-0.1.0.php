<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
--
-- Creating table `mylerz_neighborhood`
--
CREATE TABLE {$this->getTable("mylerz_shipment/mylerzneighborhood")} (
	`mylerz_neighborhood_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`mylerz_neighborhood_code` VARCHAR(20) NOT NULL,
	`mylerz_neighborhood_name_id` MEDIUMINT(8) UNSIGNED NULL,
	`mylerz_neighborhood_name_en` VARCHAR(50) NOT NULL,
	`mylerz_neighborhood_name_ar` VARCHAR(50) NOT NULL,
	`mylerz_area_code` VARCHAR(10) NOT NULL,
	`mylerz_area_name_en` VARCHAR(50) NOT NULL,
	`mylerz_area_name_ar` VARCHAR(50) NOT NULL,
	`mylerz_city_code` VARCHAR(10) NOT NULL,
	`mylerz_city_name_id` INT UNSIGNED NOT NULL,
	`mylerz_city_name_en` VARCHAR(25) NOT NULL,
	`mylerz_city_name_ar` VARCHAR(25) NOT NULL,
	`mylerz_country_id` VARCHAR(2) NOT NULL,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP  NOT NULL on update CURRENT_TIMESTAMP, 
      PRIMARY KEY (`mylerz_neighborhood_id`),
      CONSTRAINT FK_MylerzNeighborhood
          FOREIGN KEY (`mylerz_neighborhood_name_id`)
          REFERENCES `directory_country_region_city`(`city_id`),
      CONSTRAINT FK_MylerzCityName
          FOREIGN KEY (`mylerz_city_name_id`)
          REFERENCES `directory_country_region`(`region_id`),
      CONSTRAINT FK_MylerzCountry
          FOREIGN KEY (`mylerz_country_id`)
          REFERENCES `directory_country`(`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mylerz Neighborhood Table';

SQLTEXT;
$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();