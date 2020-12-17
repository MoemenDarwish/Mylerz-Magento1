<?php

class Mylerz_Shipment_Block_Adminhtml_Neighborhoods_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("mylerz_shipment_neighborhoods_grid");
        $this->setDefaultSort("mylerz_neighborhood_id");//mylerz_shipment_neighborhoods_id
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);

//        $this->setUseAjax(true);
//        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("mylerz_shipment/mylerzneighborhood")->getCollection();
//        $countryCollection = Mage::getModel('directory/country')->getCollection();
//        echo $countryCollection->getSelect()->__toString();
        // $collection->getSelect()->joinInner(
        //     ["region_table" => Mage::getSingleton('core/resource')->getTableName("directory/country_region")],
        //     "main_table.region_id = region_table.region_id AND region_table.country_id = 'EG'",
        //     ["region_name" => "region_table.default_name"]
        // );
        // $collection->getSelect()->joinInner(
        //     ["region_table" => Mage::getSingleton('core/resource')->getTableName("directory/country_region")],
        //     "main_table.region_id = region_table.region_id AND region_table.country_id = 'EG'",
        //     ["region_name" => "region_table.default_name"]
        // );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("mylerz_neighborhood_id", array(
            "header" => Mage::helper("mylerz_shipment")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "mylerz_neighborhood_id",
        ));
        $this->addColumn("mylerz_neighborhood_code", array(
            "header" => Mage::helper("mylerz_shipment")->__("Area Code"),
            "index" => "mylerz_neighborhood_code",
        ));
        $this->addColumn("mylerz_neighborhood_name_en", array(
            "header" => Mage::helper("mylerz_shipment")->__("Area Name En"),
            "index" => "mylerz_neighborhood_name_en",
        ));
        $this->addColumn("mylerz_neighborhood_name_ar", array(
            "header" => Mage::helper("mylerz_shipment")->__("Area Name ar"),
            "index" => "mylerz_neighborhood_name_ar",
        ));
        $this->addColumn("mylerz_area_code", array(
            "header" => Mage::helper("mylerz_shipment")->__("Zone Code"),
            "index" => "mylerz_area_code",
        ));
        $this->addColumn("mylerz_area_name_en", array(
            "header" => Mage::helper("mylerz_shipment")->__("Zone Name En"),
            "index" => "mylerz_area_name_en",
        ));
        $this->addColumn("mylerz_area_name_ar", array(
            "header" => Mage::helper("mylerz_shipment")->__("Zone Name ar"),
            "index" => "mylerz_area_name_ar",
        ));
        $this->addColumn("mylerz_city_code", array(
            "header" => Mage::helper("mylerz_shipment")->__("City Code"),
            "index" => "mylerz_city_code",
        ));
        $this->addColumn("mylerz_city_name_en", array(
            "header" => Mage::helper("mylerz_shipment")->__("City Name En"),
            "index" => "mylerz_city_name_en",
        ));
        $this->addColumn("mylerz_city_name_ar", array(
            "header" => Mage::helper("mylerz_shipment")->__("City Name ar"),
            "index" => "mylerz_city_name_ar",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    
}