<?php

class Mylerz_Shipment_Block_Adminhtml_Neighborhoods_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("neighborhoods_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("mylerz_shipment")->__("Mylerz Neighborhood Information"));
    }

    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
            "label" => Mage::helper("mylerz_shipment")->__("Mylerz Neighborhood Information"),
            "title" => Mage::helper("mylerz_shipment")->__("Mylerz Neighborhood Information"),
            "content" => $this->getLayout()->createBlock("mylerz_shipment/adminhtml_neighborhoods_edit_tab_form")->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
