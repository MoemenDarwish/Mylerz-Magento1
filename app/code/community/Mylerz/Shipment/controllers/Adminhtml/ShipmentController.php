<?php
class Mylerz_Shipment_Adminhtml_ShipmentController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		// return Mage::getSingleton('admin/session')->isAllowed('mylerz_shipment/shipment');
		return true;
	}

	public function postOrdersAction()
    {
        $_params = $this->getRequest()->getParams();
        $_ordersIds = $_params["order_ids"];
        if (0 == count($_ordersIds))
            Mage::throwException($this->__('No orders were selected for create Mylerz shipment.'));

        $_response = json_decode($this->_getToken());
        $authorization = null;
        if($_response && $_response->access_token)
        	$authorization = sprintf('Authorization: %s %s', $_response->token_type, $_response->access_token);
        else
        	Mage::throwException($this->__('Authentication Failed.'));

        $_errorMsg = "";
        $_successMsg = "Mylerz Shipment Number(s): ";
        $_oneShipmentAtleastCreated = 0;
        $_packages = [];
        $_pakagesIds = [];
        $_ordersItems = [];
        foreach ($_ordersIds as $_id) {
            $_order = Mage::getModel('sales/order')->load($_id);
            $_orderIncrementId = $_order->getIncrementId();
            if (!$_order->canShip()) {
                $_errorMsg .= "Unable to Ship this order #$_orderIncrementId, ";
                continue;
            }
            $_ordersItems[$_orderIncrementId] = ["obj" => $_order, "qty" => $_order->getItemsCollection()->count()];
            $_description = "";
            $itemsnamecounter = 1;
            $item_supplier_id = '';
            foreach ($_order->getAllVisibleItems() as $itemname) {
                if ($itemname->getQtyOrdered() > $itemname->getQtyShipped()) {
                    $_description .= $itemname->getId() . ' - ' . trim($itemname->getName());
                }
                $itemsnamecounter++;
                $supplier_different = false;
                $atleast_item_shipped = false;
                if (!$item_supplier_id) {
                    $item_supplier_id = $itemname->getUdropshipVendor();
                } else {
                    if ($item_supplier_id != $itemname->getUdropshipVendor()) {
                        if (!$supplier_different) {
                            $supplier_different = true;
                        }
                    }
                }
                if ($itemname->getQtyOrdered() == $itemname->getQtyShipped()) {
                    if (!$atleast_item_shipped) {
                        $atleast_item_shipped = true;
                    }
                }
            }//endForeach
            $_serviceType = "DTD";//Door to door
            //$_serviceType = "DTC";//Door to counter
            //$_serviceType = "CTD";//Counter to door
            //$_serviceType = "CTC";//Counter to counter
            $_service = "ND";//Next Day
            //$_service = "SD";//Same Day
            $_service_Category = "DELIVERY";//Forward delivery
            //$_service_Category = "RETURN";//Reverse delivery
            $_payment_Type = "PP";//Pre-Paid
            $_payment_Type = "COD";//Cash-on-Delivery
            $_payment_Type = "CC";//CC-on-Delivery
            if ("cashondelivery" == $_order->getPayment()->getMethodInstance()->getCode()) {
                $_payment_Type = "COD";
                $_amount = round($_order->getData('grand_total'), 2);
            } else {
                $_payment_Type = "PP";
                $_amount = "";
            }
            //$_amount=($_order->getPayment()->getMethodInstance()->getCode() != 'ccsave')?round($_order->getData('grand_total'), 2):'';
            if (Mage::getStoreConfig('mylerzsettings/config/sandbox_flag') == 1)
                $_amount = '';

            $_shipping = $_order->getShippingAddress();
            $_name = ($_shipping) ? $_shipping->getName() : "";
            $_phone = ($_shipping) ? $_shipping->getData("telephone") : "";
            $_street = ($_shipping) ? $_shipping->getData("street") : "";
			$_country = ($_shipping) ? $_shipping->getCountry() : "";
			if(!empty($_shipping->getData('city'))){
				$_mylerzNeighborhoodCollection = Mage::getModel("mylerz_shipment/mylerzneighborhood")->getCollection();
				$_mylerzNeighborhoodCollection->addFieldToFilter("mylerz_city_name_id", $_shipping->getData('region_id'));
				$_mylerzNeighborhoodDefault = $_mylerzNeighborhoodCollection->getFirstItem();
				//check count if>1 ==> make join if not get data & out
				if($_mylerzNeighborhoodCollection->getSize()>1){
					$_mylerzNeighborhoodCollection->getSelect()->join(
						['r' => 'directory_country_region_city'], 
						'r.region_id = `main_table`.mylerz_city_name_id AND r.cityname = "'.$_shipping->getData('city').'" ', []
					);//->where("r.cityname = '".$_shipping->getData('city')."' ");
					$_mylerzNeighborhood = ($_mylerzNeighborhoodCollection->getSize()>=1)?$_mylerzNeighborhoodCollection->getFirstItem():$_mylerzNeighborhoodDefault;
				} elseif(1==$_mylerzNeighborhoodCollection->getSize()) {
					$_mylerzNeighborhood = $_mylerzNeighborhoodCollection->getFirstItem();
				} else {
					$_mylerzNeighborhood = $_mylerzNeighborhoodDefault;
				}
			} else {
				$_mylerzNeighborhoodCollection = Mage::getModel("mylerz_shipment/mylerzneighborhood")->getCollection()
					->addFieldToFilter("mylerz_city_name_id", $_shipping->getData('region_id'));
				$_mylerzNeighborhood = $_mylerzNeighborhoodCollection->getFirstItem();
			}

            $_address_Category = "H";//Home
			//$_address_Category = "OF";//Office
			
            $post = [
                'WarehouseName' => '',
                'PickupDueDate' => date("Y-m-d\TH:i:s"),
                'Package_Serial' => 1,
                "Reference" => $_orderIncrementId,
                'Description' => $_description,
                'Total_Weight' => 0,
                'Service_Type' => $_serviceType,
                'Service' => $_service,
                'ServiceDate' => date("Y-m-d\TH:i:s"),
                'Service_Category' => $_service_Category,
                'Payment_Type' => $_payment_Type,
                'COD_Value' => $_amount,
                'Customer_Name' => $_name,
                'Mobile_No' => $_phone,
                'Building_No' => "",
                'Street' => $_street,
                'Floor_No' => "",
                'Apartment_No' => "",
                'Country' => $_country,
				'City'   => "",//$_mylerzNeighborhood->getData('mylerz_city_code'),
				'Neighborhood'   => $_mylerzNeighborhood->getData('mylerz_neighborhood_code'),
                'District' => "",
                'GeoLocation' => "",
                'Address_Category' => $_address_Category,
                "CustVal" => "",
                "Currency" => "",
                'Pieces' => [[
                    "PieceNo" => 1,
                    "Weight" => "0",
                    "ItemCategory" => "0",
                    "SpecialNotes" => "0",
                    "Dimensions" => "0"
                ]]
            ];
            $_packages[] = $post;
            $_pakagesIds[] = $_orderIncrementId;
        }//endForeach

        $response = $this->_postRequest('http://41.33.122.61:58639/api/Orders/AddOrders', $_packages, $authorization);
        $response = json_decode($response);

        if ($response->IsErrorState)
            Mage::throwException($response->ErrorDescription);

        if (!empty($_errorMsg)) {
            //trim last to char & add \n
            $_errorMsg = substr($_errorMsg, 0, -2) . "\n";
        }
        foreach ($response->Value->Packages as $i => $_package) {
            if (!$_package->BarCode) {
                $_errorMsg .= "Shipment Creation Failed for this order #" . $_pakagesIds[$i] . ", ";
                continue;
            }
            // Create shipment process
            $shipment = Mage::getModel('sales/service_order', $_ordersItems[$_pakagesIds[$i]]["obj"])->prepareShipment($_ordersItems[$_pakagesIds[$i]]["qty"]);
            $shipment = new Mage_Sales_Model_Order_Shipment_Api();
            $shipmentId = $shipment->create($_pakagesIds[$i]);
            $trackModel = Mage::getModel('sales/order_shipment_api')
                ->addTrack($shipmentId, 'mylerz', 'Mylerz', $_package->BarCode);
            $_successMsg .= $_package->BarCode . ", ";
            $_oneShipmentAtleastCreated = 1;
        }//endForeach
        $_errorMsg = substr($_errorMsg, 0, -2);
        $_successMsg = (1 == $_oneShipmentAtleastCreated) ? substr($_successMsg, 0, -2) . " has been created." : "";
        if (!empty($_successMsg))
            Mage::getSingleton('core/session')->addSuccess($_successMsg);
        if (!empty($_errorMsg))
            Mage::getSingleton('core/session')->addError($_errorMsg);
        $this->_redirect('adminhtml/sales_order/');
    }

	public function postAction()
    {
	   $_params = $this->getRequest()->getParams();

	   $order = Mage::getModel('sales/order')->loadByIncrementId($_params['mylerz_shipment_original_reference']);

	   if(!$order->canShip())
	   		Mage::throwException($this->__('Unable to Ship this order.'));

	   $_response = json_decode($this->_getToken());

		$authorization = null;
		if($_response && $_response->access_token)
			$authorization = sprintf('Authorization: %s %s', $_response->token_type, $_response->access_token);
		else
			Mage::throwException($this->__('Authentication Failed.'));
	   
	   $post = [
		   	'WarehouseName' => '',
			'PickupDueDate' => date("Y-m-d\TH:i:s"),
			'Package_Serial' => 1,
			"Reference" => $_params['mylerz_shipment_original_reference'],
			'Description'   => $_params['mylerz_shipment_description'],
			'Total_Weight' => 0,
			'Service_Type'   => $_params['mylerz_shipment_info_product_type'],
			'Service'   => $_params['mylerz_shipment_info_service_type'],
			'ServiceDate' => date("Y-m-d\TH:i:s"),
			'Service_Category'   => $_params['mylerz_shipment_info_payment_option'],
			'Payment_Type'   => $_params['mylerz_shipment_info_payment_type'],
			'COD_Value'   => $_params['mylerz_shipment_info_cod_amount'],
			'Customer_Name'   => $_params['mylerz_shipment_receiver_name'],
			'Mobile_No'   => $_params['mylerz_shipment_receiver_phone'],
			'Building_No' => "",
			'Street'   => $_params['mylerz_shipment_receiver_street'],
			'Floor_No' => "",
			'Apartment_No' => "",
			'Country'   => $_params['mylerz_shipment_receiver_country'],
			'City'   => $_params['mylerz_shipment_shipper_city'],
			'Neighborhood'   => $_params['mylerz_shipment_receiver_state'],
			'District' => "",
			'GeoLocation' => "",
			'Address_Category'   => $_params['mylerz_shipment_info_product_group'],
			"CustVal" => "",
			"Currency" => "",
			'Pieces'   => [[
				"PieceNo" => 1,
				"Weight" => "0",
				"ItemCategory" => "0",
				"SpecialNotes" => "0",
				"Dimensions" => "0"
			]]
		];

		$response = $this->_postRequest('http://41.33.122.61:58639/api/Orders/AddOrders', array($post), $authorization);
		$response = json_decode($response);

		if($response->IsErrorState)
			Mage::throwException($response->ErrorDescription);

		if(!$response->Value->Packages[0]->BarCode)
			Mage::throwException($this->__('Shipment Creation Failed.'));

		// Create shipment process
		$itemQty =  $order->getItemsCollection()->count();
		$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
		$shipment = new Mage_Sales_Model_Order_Shipment_Api();
		$shipmentId = $shipment->create($_params['mylerz_shipment_original_reference']);

		$trackmodel = Mage::getModel('sales/order_shipment_api')
							->addTrack($shipmentId,'mylerz','Mylerz', $response->Value->Packages[0]->BarCode);
							
		Mage::getSingleton('core/session')->addSuccess('Mylerz Shipment Number: '.$response->Value->Packages[0]->BarCode.' has been created.');

		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
	}
	
	public function _getToken()
	{
		$post = [
			'username' => Mage::getStoreConfig('mylerz_section/mylerz_credentials/username'),
			'password' => Mage::getStoreConfig('mylerz_section/mylerz_credentials/password'),
			'grant_type' => 'password',
		];

		$_reponse = $this->_postRequest("http://41.33.122.61:58639/Token", $post);

		return $_reponse;
	}

	public function _postRequest($link, $post, $authorization = null)
	{
		try {
			$ch = curl_init();
			
			// Check if initialization had gone wrong*    
			if ($ch === false) {
				Mage::throwException('failed to initialize');
			}
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if($authorization) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
			}
			else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			}

			// execute!
			$response = curl_exec($ch);
			
			// Check the return value of curl_exec(), too
			if ($response === false) {
				Mage::throwException(curl_error($ch), curl_errno($ch));
			}

			// close the connection, release resources used
			curl_close($ch);

			// do anything you want with your response
			return $response;
		
		} catch(Exception $e) {
		
			Mage::throwException(sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);
		
		}
	}
	
	public function printLabelAction()
    {
		$_params = $this->getRequest()->getParams();

		$order = Mage::getModel('sales/order')->load($_params['order_id']);
		$_tracks = $order->getTracksCollection()->addAttributeToFilter('carrier_code', 'mylerz')->getFirstItem();

		if(!$_tracks)
			Mage::throwException($this->__('No Tracking Number Found.'));

		$_awb = $_tracks->getNumber();
			
		$post = [
			'Barcode' => "$_awb",
			'ReferenceNumber' => '',
		];

		$_response = json_decode($this->_getToken());

		$authorization = null;
		if($_response && $_response->access_token)
			$authorization = sprintf('Authorization: %s %s', $_response->token_type, $_response->access_token);
		else
			Mage::throwException($this->__('Authentication Failed.'));

		// echo "<pre>";	print_r($_response);
		$response = $this->_postRequest('http://41.33.122.61:58639/api/packages/GetAWB', json_encode($post), $authorization);
		$response = json_decode($response);
		// print_r( json_encode($post));

		// print_r( $response); die;
		if($response->IsErrorState)
			Mage::throwException($response->ErrorDescription);
		$name = "{$order->getIncrementId()}-shipment-label.pdf";
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$name.'"');
		readfile($response->Value);
		exit();

		// $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
	}
}