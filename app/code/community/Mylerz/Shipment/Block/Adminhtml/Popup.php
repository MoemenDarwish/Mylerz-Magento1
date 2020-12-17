<?php

class Mylerz_Shipment_Block_Adminhtml_Popup extends Mage_Adminhtml_Block_Template {
	
	/**
	 *
	 * @return array
	 */
	public function getcitis() {
		$cities = array(
			'CA' => 'Cairo',
			'Giza' => 'Giza',
			'ALX' => 'Alexandria',
			'ASYT' => 'Asyut',
			'ASWN' => 'Aswan',
			'BEHR' => 'Beheira',
			'BENS' => 'Beni Suef',
			'DAKH' => 'Dakahlia',
			'DAMT' => 'Damietta',
			'FAYM' => 'Faiyum',
			'GHRB' => 'Gharbia',
			'ISML' => 'Ismailia',
			'SHKH' => 'Kafr El Sheikh',
			'LUXR' => 'Luxor',
			'MTRH' => 'Matruh',
			'MNYA' => 'Minya',
			'MONF' => 'Monufia',
			'WADI' => 'El Wadi el Gedid',
			'NSNA' => 'North Sinai',
			'PORS' => 'Port Said',
			'QLYB' => 'Qalyubia',
			'QENA' => 'Qena',
			'REDS' => 'El Bahr El Ahmar',
			'SHRK' => 'Sharqia',
			'SOHG' => 'Sohag',
			'SSINA' => 'South Sinai',
			'SUEZ' => 'Suez'
		);
		return $cities;
	}
}