<?php
class Mylerz_Shipment_Model_Observer
{
	public function addMassCreateShipment(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
 
        if($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
            && $block->getRequest()->getControllerName() == 'sales_order')
        {
            $block->addItem('exportreviewcsv', array(
                'label' => 'Create Mylerz Shipments',
                'url' => $block->getUrl('admin_mylerz/adminhtml_shipment/postOrders')
			));
		}
	}

	public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();

		if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
			
			$itemscount 	= 0;
			$totalWeight 	= 0;
			$_order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParam('order_id'));				
			$itemsv = $_order->getAllVisibleItems();
			foreach($itemsv as $itemvv){
				if($itemvv->getQtyOrdered() > $itemvv->getQtyShipped()){
					$itemscount += $itemvv->getQtyOrdered() - $itemvv->getQtyShipped();
				}
			}

			if($_order->canShip()) {
				$block->addButton('_mylerzShipmentbtn', array(
					'label' => Mage::helper('sales')->__('Prepare Mylerz Shipment'),
					'onclick' => 'mylerzpop('.$itemscount.')',
					'class' => 'go',
				));
			}

			
			if($_order->hasShipments()) {
				$block->addButton('_mylerzLabelbtn', array(
					'label' => Mage::helper('sales')->__('Print Mylerz label'),
					'onclick' => 'mylerzObj.printLabel()',
					'class' => 'go',
				));
			}
		}
	}

    /**
     *
     * @param Varien_Event_Observer $observer
     * NOTE:
     *  - query is
     *      >> SELECT `main_table`.*, ROUND(total_qty_ordered,0) AS `total_qty_ordered`, `sfosa`.`telephone` AS `shipping_telephone`, `sfop`.`method` AS `payment_method`,
     *              `sfop`.`additional_data` AS `payment_additional_data`, `sfop`.`premium_no`, `pps`.`pps_serial`, `pps`.`pps_warehouse`, `sfoi`.*, `esfoi`.`preparation_warehouse`,
     * 				`sfst`.`track_number`, `posno`.`ref_no` AS `pos_ref_no`, `deliverydate`.`date`, `deliverydate`.`time`, `deliverydate`.`comment`,
     * 				`sfst`.`carrier_code`
     *          FROM `sales_flat_order_grid` AS `main_table`
     *          INNER JOIN `sales_flat_order` AS `sfo` ON sfo.entity_id=`main_table`.entity_id
     *          INNER JOIN `sales_flat_order_address` AS `sfosa` ON sfosa.parent_id=`main_table`.entity_id and sfosa.address_type = "shipping"
     *          INNER JOIN `sales_flat_order_payment` AS `sfop` ON sfop.parent_id=`main_table`.entity_id
     *          LEFT JOIN `purchase_product_serial` AS `pps` ON pps.pps_salesorder_id=`main_table`.entity_id
     * 			LEFT JOIN `sales_flat_order_item` AS `sfoi` ON sfoi.order_id=`main_table`.entity_id
     * 			LEFT JOIN `erp_sales_flat_order_item` AS `esfoi` ON esfoi.esfoi_item_id=`sfoi`.item_id
     * 			LEFT JOIN `sales_flat_shipment_track` AS `sfst` ON sfst.order_id=`main_table`.entity_id
     * 			LEFT JOIN `pos_ref_no` AS `posno` ON posno.order_entity_id=main_table.entity_id
     * 			LEFT JOIN `amasty_amdeliverydate_deliverydate` AS `deliverydate` ON (main_table.entity_id = deliverydate.order_id)
     * 			GROUP BY `main_table`.`entity_id`
     *
     */
    public function GetMylerzShipmentAWB(Varien_Event_Observer $observer)
    {
        $collection = $observer->getOrderGridCollection();
        $select = $collection->getSelect()->columns(['carrier_code' => 'sfst.carrier_code']);
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function addColumnToSalesOrderGrid(Varien_Event_Observer $observer)
    {
        $gridBlock = $observer->getBlock();
        if (!($gridBlock instanceof Mage_Adminhtml_Block_Sales_Order_Grid || $gridBlock instanceof Noble_AdminOrderGrid_Block_Sales_Order_Grid)) return;
        Mage::helper("stylisheve_core")->addColumnToOrderGrid($gridBlock, 'carrier_code', 'Mylerz AWB', 'track_number', [Mage::helper("stylisheve_core"), 'numberToString']);
        // Mage::helper("stylisheve_core")->addColumnToOrderGrid($gridBlock, 'confirmed_time', 'Mylerz AWB', 'track_number', [Mage::helper("stylisheve_core"), 'numberToString']);
    }

    /**
     * @param $value
     * @param Varien_Object $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return String
     */
    public function stringToUrl($value, Varien_Object $row, Mage_Adminhtml_Block_Widget_Grid_Column $column)
    {
        if ("mylerz" == $value) {
            //$link = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getEntityId()));
            //return '<a href="' . $link . '" target="_blank">' . 'Print Mylerz AWB' . '</a>';
        }
    }

}
