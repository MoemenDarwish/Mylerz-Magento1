<?php

class Mylerz_Shipment_Block_Adminhtml_Neighborhoods_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "neighborhoods_id";
        $this->_blockGroup = "mylerz_shipment";
        $this->_controller = "adminhtml_neighborhoods";
        $this->_updateButton("save", "label", Mage::helper("mylerz_shipment")->__("Save Mylerz Neighborhood Code"));
        $this->_updateButton("delete", "label", Mage::helper("mylerz_shipment")->__("Delete Mylerz Neighborhood Code"));

        $this->_addButton("saveandcontinue", array(
            "label" => Mage::helper("mylerz_shipment")->__("Save And Continue Edit"),
            "onclick" => "saveAndContinueEdit()",
            "class" => "save",
        ), -100);

        $this->_formScripts[] = "
							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
    }

    public function getHeaderText()
    {
        if (Mage::registry("mylerzneighborhood_data") && Mage::registry("mylerzneighborhood_data")->getId()) {
            return Mage::helper("mylerz_shipment")->__("Edit Mylerz Neighborhood '%s'", $this->htmlEscape(Mage::registry("mylerzneighborhood_data")->getId()));
        } else {
            return Mage::helper("mylerz_shipment")->__("Add Mylerz Neighborhood");
        }
    }
}