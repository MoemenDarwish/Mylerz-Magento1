<?php


class Mylerz_Shipment_Block_Adminhtml_Neighborhoods extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = "adminhtml_neighborhoods";
        $this->_blockGroup = "mylerz_shipment";
        $this->_headerText = Mage::helper("mylerz_shipment")->__("Neighborhoods Manager");
        $this->_addButtonLabel = Mage::helper("mylerz_shipment")->__("Add New Mylerz Neighborhood");
        parent::__construct();
        //$this->_removeButton('add');
    }

}