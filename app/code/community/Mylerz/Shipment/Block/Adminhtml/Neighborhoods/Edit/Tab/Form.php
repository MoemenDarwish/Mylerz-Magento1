<?php

class Mylerz_Shipment_Block_Adminhtml_Neighborhoods_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("mylerz_shipment_form", array("legend" => Mage::helper("mylerz_shipment")->__("Mylerz Neighborhood information")));

        $fieldset->addField("mylerz_neighborhood_name_id", "select", array(
            "label" => Mage::helper("mylerz_shipment")->__("City"),
            "name" => "mylerz_neighborhood_name_id",
            "values" => Mage::helper("stylisheve_core")->getEgyptCities(),
        ));

        $fieldset->addField("mylerz_neighborhood_code", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Neighborhood Code"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_neighborhood_code",
        ));

        $fieldset->addField("mylerz_neighborhood_name_en", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Neighborhood Name En"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_neighborhood_name_en",
        ));

        $fieldset->addField("mylerz_neighborhood_name_ar", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Neighborhood Name Ar"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_neighborhood_name_ar",
        ));
        
        $fieldset->addField("mylerz_area_code", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Area Code"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_area_code",
        ));

        $fieldset->addField("mylerz_area_name_en", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Area Name En"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_area_name_en",
        ));

        $fieldset->addField("mylerz_area_name_ar", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("Area Name Ar"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_area_name_ar",
        ));

        $fieldset->addField("mylerz_city_name_id", "select", array(
            "label" => Mage::helper("mylerz_shipment")->__("State"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_city_name_id",
            "values" => Mage::helper("stylisheve_core")->getRegions(),
        ));

        $fieldset->addField("mylerz_city_code", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("City Code"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_city_code",
        ));

        $fieldset->addField("mylerz_city_name_en", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("City Name En"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_city_name_en",
        ));

        $fieldset->addField("mylerz_city_name_ar", "text", array(
            "label" => Mage::helper("mylerz_shipment")->__("City Name Ar"),
            "class" => "required-entry",
            "required" => true,
            "name" => "mylerz_city_name_ar",
        ));

        $fieldset->addField("mylerz_country_id", "hidden", array(
            "label" => Mage::helper("mylerz_shipment")->__("Country Code"),
            //"class" => "required-entry",
            //"required" => true,
            "name" => "mylerz_country_id",
            "value" => 'EG'
        ));


        if (Mage::getSingleton("adminhtml/session")->getMylerzneighborhoodData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getMylerzneighborhoodData());
            Mage::getSingleton("adminhtml/session")->getMylerzneighborhoodData(null);
        } elseif (Mage::registry("mylerzneighborhood_data")) {
            $form->setValues(Mage::registry("mylerzneighborhood_data")->getData());
        }
        return parent::_prepareForm();
    }
}
