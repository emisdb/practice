<?php
return array(
	'room_sale'=>array(
//		"SELECT * FROM gcn_rooms, gcn_rooms_ext
// WHERE  gcn_rooms_ext.adv_for_emls =1 AND gcn_rooms.id = gcn_rooms_ext.id AND objects_status = 0 AND removal_request = '1';";
// amount_rooms_total
		'gcn_rooms'	=>
			array(
				'Category'			 => array(1,'roomSale'),
				'ExternalId'		 => array(0,'id'),
				'Description'		 => array(0,'comment_for_clients') ,
				'Address'			 => array(2,array( 'id_reg','str_reg','str_dept', 'str_district', 'str_street', 'house_number', 'house_korpus', 'house_letter' )),
				"CadastralNumber"	 => array(0,'cadastral_number'),
				"RoomsForSaleCount"	 => array(0,'amount_rooms'),
				"RoomsCount"		 => array(0,'amount_rooms_total'),
				"FlatRoomsCount"	 => array(0,'amount_rooms_total'),
				"RoomType"		 	 => array(5,'flat_type','id_flat_type'),
				"RoomArea"			 => array(0,'s_rooms'),
				"TotalArea"			 => array(0,'s_all'),
				"FloorNumber"		 => array(0,'floor'),
				"KitchenArea"		 => array(0,'s_kitchen'),
				"LoggiasCount"		 => array(5,'gcn_list_balcony_l','id_balcony'),
				"BalconiesCount"	 => array(5,'gcn_list_balcony_b','id_balcony'),
				"SeparateWcsCount"	 => array(5,'bathrooms_sep','id_bathroom'),
				"CombinedWcsCount"	 => array(5,'bathrooms_comb','id_bathroom'),
				"RepairType"		 => array(5,'gcn_list_repair','id_repair'),
				"HasPhone"			 => array(5,'gcn_list_phone','id_phone'),
				"HasRamp"			 => array(0,'ramp'),
				'Building'			=> array(3,array(
					array('FloorsCount'		=> array(0,"floor_all")),
					array('BuildYear'		=> array(0,"year_build")),
					array('CeilingHeight' 	=> array(0,"s_ceiling")),
					array('MaterialType'	=> array(5,"gcn_list_type_house", "id_type_house")),
					array('Series'			=> array(5,"gcn_list_type_house_s", "id_type_house")),
					array('HasGarbageChute' => array(5,"refuse_chute", "id_refuse_chute")),
					array('PassengerLiftsCount' => array(5,"gcn_list_lift", "id_lift")),
					array('ParkingType'		=> array(5,"gcn_parking", "id_parking")),
				)),
				'BargainTerms'				=> array(3,array(
					array('Price'			=> array(0,"price")),
					array('MortgageAllowed'	=> array(0,"hypothec")),
					array('SaleType'		=> array(5,"type_flat", "id_type_flat")),
					array('Currency'		=> array(1,"rur")),
					array('AgentBonus'			=> array(3,array(
						array('Value'			=> array(0,"reward",1)),
						array('PaymentType'		=> array(5,"type_reward","reward_id")),
						array('Currency'		=> array(1,"rur")),
					))),
				)),				'config_where'		 => array(array('removal_request', 1),array('objects_status', 0)),
			),
		'gcn_rooms_ext'	=>
			array(
				'config_from'		=> array('id', 'gcn_rooms', 'id'),
				'config_where'		=> array(array('adv_for_emls', 1)),
			),
		'gcn_kadr'	=>
			array(
				'config_from'		=> array('id_agent', 'gcn_rooms', 'id_user'),
				'config_where'		=> array(array('agent_is_active', 1),array('id_role', 3)),
				'Phones'			=> array(4,array(
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone2'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone3'))),
				)),
				'SubAgent'			=> array(3,array(
					array('Phone' 		=> array(0,"phone")),
					array('Email' 		=> array(0,"email1")),
					array('FirstName' 	=> array(0,"name")),
					array('LastName' 	=> array(0,"lastname")),
				)),
			),
	),
	'flat_sale'=>array(
		'gcn_flats'	=>
			array(
				'Category'			 => array(0,'is_part'),
				'ExternalId'		 => array(0,'id'),
				'Description'		 => array(0,'comment_for_clients') ,
				'Address'			 => array(2,array( 'id_reg','str_reg','str_dept', 'str_district', 'str_street', 'house_number', 'house_korpus', 'house_letter', 'flat_number' )),
//				'Address'			 => array(2,array('str_reg', 'str_dept', 'str_district', 'str_street', 'house_number', 'house_korpus', 'house_letter', 'flat_number' )),
// id_type_flat
				"CadastralNumber"	 => array(0,'cadastral_number'),
				"RoomType"		 	 => array(5,'flat_type','id_flat_type'),
				"IsApartments"		 => array(0,'apartment'),
				"IsPenthouse"		 => array(0,'penthouse'),
				"FlatRoomsCount"	 => array(0,'amount_rooms'),
				"TotalArea"			 => array(0,'s_all'),
				"FloorNumber"		 => array(0,'floor'),
				"LivingArea"		 => array(0,'s_life'),
				"KitchenArea"		 => array(0,'s_kitchen'),
				"LoggiasCount"		 => array(5,'gcn_list_balcony_l','id_balcony'),
				"BalconiesCount"	 => array(5,'gcn_list_balcony_b','id_balcony'),
				"AllRoomsArea"		 => array(0,'s_rooms'),
				"SeparateWcsCount"	 => array(5,'bathrooms_sep','id_bathroom'),
				"CombinedWcsCount"	 => array(5,'bathrooms_comb','id_bathroom'),
				"WindowsViewType"	 => array(5,'gcn_list_view_from_window','id_view_from_window'),
				"RepairType"		 => array(5,'gcn_list_repair','id_repair'),
				"HasPhone"			 => array(5,'gcn_list_phone','`id_phone`'),
				"HasRamp"			 => array(0,'ramp'),
				'Building'			=> array(3,array(
					array('FloorsCount'		=> array(0,"floor_all")),
					array('BuildYear'		=> array(0,"year_build")),
					array('CeilingHeight' 	=> array(0,"s_ceiling")),
					array('MaterialType'	=> array(5,"gcn_list_type_house", "id_type_house")),
					array('Series'			=> array(5,"gcn_list_type_house_s", "id_type_house")),
					array('HasGarbageChute' => array(5,"refuse_chute", "id_refuse_chute")),
					array('PassengerLiftsCount' => array(5,"gcn_list_lift", "id_lift")),
					array('ParkingType'		=> array(5,"gcn_parking", "id_parking")),
				)),
				'BargainTerms'				=> array(3,array(
					array('Price'			=> array(0,"price")),
					array('MortgageAllowed'	=> array(0,"hypothec")),
					array('SaleType'		=> array(5,"type_flat", "id_type_flat")),
					array('Currency'		=> array(1,"rur")),
					array('AgentBonus'			=> array(3,array(
						array('Value'			=> array(0,"reward",1)),
						array('PaymentType'		=> array(5,"type_reward","reward_id")),
						array('Currency'		=> array(1,"rur")),
					))),
				)),
				'config_where'		 => array(array('removal_request', 1),array('objects_status', 0)),
				),
		'gcn_flats_ext'	=>
			array(
				'config_from'		=> array('id', 'gcn_flats', 'id'),
				'config_where'		=> array(array('adv_for_emls', 1)),
			),
		'gcn_kadr'	=>
			array(
				'config_from'		=> array('id_agent', 'gcn_flats', 'id_user'),
				'config_where'		=> array(array('agent_is_active', 1),array('id_role', 3)),
				'Phones'			=> array(4,array(
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone2'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone3'))),
				)),
				'SubAgent'			=> array(3,array(
					array('Phone' 		=> array(0,"phone")),
					array('Email' 		=> array(0,"email1")),
					array('FirstName' 	=> array(0,"name")),
					array('LastName' 	=> array(0,"lastname")),
				)),
				),
		),

// "SELECT * FROM gcn_farm, gcn_farm_ext WHERE  gcn_farm_ext.adv_for_emls =1 AND gcn_farm.id = gcn_farm_ext.id AND objects_status = 0  AND removal_request = '1';";
// id_type_flat

	'house_sale'=>array(
		'gcn_farm'	=>
			array(
				'Category'			 => array(5,'gcn_list_object','id_obj_type'),
				'ExternalId'		 => array(0,'id'),
				'Description'		 => array(0,'comment_for_clients') ,
				'Address'			 => array(2,array( 'id_reg','str_reg','str_dept', 'str_district', 'str_street', 'house_number', 'house_korpus', 'house_letter' )),
				"CadastralNumber"	 => array(0,'cadastral_number'),
				"FlatRoomsCount"	 => array(0,'amount_rooms'),
				"TotalArea"			 => array(0,'s_total'),
				"AllRoomsArea"		 => array(0,'s_rooms'),
				"KitchenArea"		 => array(0,'s_kitchen'),
				"ShareAmount"		 => array(0,'fraction'),
				"HasElectricity"	 => array(5,'gcn_list_country_electro','id_electro'),
				"HasWater"		 	=> array(5,'gcn_list_country_water','id_water'),
				'Building'			=> array(3,array(
					array('FloorsCount'		=> array(0,"floors")),
					array('BuildYear'		=> array(0,"year_build")),
				)),
				'Land'			=> array(3,array(
					array('Area'		=> array(0,"area")),
					array('AreaUnitType'		=> array(1,"sotka")),
					array('Status'		=> array(5,"gcn_list_country_type_property","id_type_property")),
				),
					array('id_obj_type' , array(4,2,3,6))
				),
				'BargainTerms'				=> array(3,array(
					array('Price'			=> array(0,"price")),
					array('MortgageAllowed'	=> array(0,"hypothec")),
					array('Currency'		=> array(1,"rur")),
					array('AgentBonus'			=> array(3,array(
						array('Value'			=> array(0,"reward",1)),
						array('PaymentType'		=> array(5,"type_reward","reward_id")),
						array('Currency'		=> array(1,"rur")),
					))),
				)),
				'config_where'		 => array(array('removal_request', 1),array('objects_status', 0)),
			),
		'gcn_farm_ext'	=>
			array(
				'config_from'		=> array('id', 'gcn_farm', 'id'),
				'config_where'		=> array(array('adv_for_emls', 1)),
			),
		'gcn_kadr'	=>
			array(
				'config_from'		=> array('id_agent', 'gcn_farm', 'id_user'),
				'config_where'		=> array(array('agent_is_active', 1),array('id_role', 3)),
				'Phones'			=> array(4,array(
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone2'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone3'))),
				)),
				'SubAgent'			=> array(3,array(
					array('Phone' 		=> array(0,"phone")),
					array('Email' 		=> array(0,"email1")),
					array('FirstName' 	=> array(0,"name")),
					array('LastName' 	=> array(0,"lastname")),
				)),
			),

	),
//	"SELECT * FROM gcn_comm, gcn_comm_ext WHERE  gcn_comm_ext.adv_for_emls =1 AND gcn_comm.id = gcn_comm_ext.id AND objects_status = 0  AND removal_request = '1' LIMIT 0,600;";

	'comm_sale'=>array(
		'gcn_comm'	=>
			array(
//				'Category'			 => array(5,'gcn_list_object','id_obj_type'),
				'ExternalId'		 => array(0,'id'),
				'Description'		 => array(0,'comment_for_clients') ,
				'Address'			 => array(2,array( 'id_reg','str_reg','str_dept', 'str_district', 'str_street', 'house_number', 'house_korpus', 'house_letter' )),
//				"CadastralNumber"	 => array(0,'cadastral_number'),
//				"FlatRoomsCount"	 => array(0,'amount_rooms'),
//				"TotalArea"			 => array(0,'s_total'),
//				"AllRoomsArea"		 => array(0,'s_rooms'),
//				"KitchenArea"		 => array(0,'s_kitchen'),
//				"ShareAmount"		 => array(0,'fraction'),
				'Building'			=> array(3,array(
					array('FloorsCount'		=> array(0,"floors")),
//					array('BuildYear'		=> array(0,"year_build")),
				)),
				'Land'			=> array(3,array(
					array('Area'		=> array(0,"area")),
					array('AreaUnitType'		=> array(1,"sotka")),
//					array('Status'		=> array(5,"gcn_list_country_type_property","id_type_property")),
				),
//					array('id_obj_type' , array(4,2,3,6))
				),
				'BargainTerms'				=> array(3,array(
//					array('Price'			=> array(0,"price")),
//					array('MortgageAllowed'	=> array(0,"hypothec")),
					array('Currency'		=> array(1,"rur")),
					array('AgentBonus'			=> array(3,array(
//						array('Value'			=> array(0,"reward",1)),
//						array('PaymentType'		=> array(5,"type_reward","reward_id")),
						array('Currency'		=> array(1,"rur")),
					))),
				)),
				'config_where'		 => array(array('removal_request', 1),array('objects_status', 0)),
				'config_where_or'		 => array(array('id_price_s', 8),array('id_price_ar_s', 8),array('id_price_s_b', 8)),
			),
		'gcn_comm_ext'	=>
			array(
				'config_from'		=> array('id', 'gcn_comm', 'id'),
				'config_where'		=> array(array('adv_for_emls', 1)),
			),
		'gcn_kadr'	=>
			array(
				'config_from'		=> array('id_agent', 'gcn_comm', 'id_user'),
				'config_where'		=> array(array('agent_is_active', 1),array('id_role', 3)),
				'Phones'			=> array(4,array(
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone2'))),
					array('PhoneSchema' => array('CountryCode' => array(1,"+7"), 'Number' => array(0,'phone3'))),
				)),
				'SubAgent'			=> array(3,array(
					array('Phone' 		=> array(0,"phone")),
					array('Email' 		=> array(0,"email1")),
					array('FirstName' 	=> array(0,"name")),
					array('LastName' 	=> array(0,"lastname")),
				)),
			),

	),


	/*

	 *
	 *


	*/
);
