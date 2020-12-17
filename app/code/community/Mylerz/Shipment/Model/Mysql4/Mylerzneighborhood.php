<?php

class Mylerz_Shipment_Model_Mysql4_Mylerzneighborhood extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("mylerz_shipment/mylerzneighborhood", "mylerz_neighborhood_id");
    }
}