<?php
//включаем вывод всех ошибок
error_reporting(E_ALL);
//подключаем функции
require_once('functions_converter_bkn_prof.php');
//создаем экземпляр класса DomDocument
$document= new DomDocument ('1.0','utf-8');
//Создаем экземпляр класса mysqli
$db=new mysqli('localhost','gcn','gcn','gcn');
$db->set_charset('utf8');
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//Создаем экземпляр класса  для генерации текущей даты по стандарту ISO8601
$objDateTime = new DateTime('NOW');
/*
Работа с кадровым блоком 
*/
//Постфикс домена электронной почты
$email_domain="@gcn-spb.ru";
//добавление корневого эл-та emls-realty-feed
		$emls_realty_feed = $document->appendChild($document->createElement('emls-realty-feed'));
		//добавление аттрибута type со значением "agent" к offer
		$emls_realty_feedAttributeXmlns = $document->createAttribute('xmlns');
		$emls_realty_feedAttributeXmlns->value = 'http://emls.ru/schemas/2015-08';
		$emls_realty_feed->appendChild($emls_realty_feedAttributeXmlns);
//добавление 	корневого эл-та generation-date-дата создания фида	
		$generationDate = $emls_realty_feed->appendChild($document->createElement('generation-date'));
		$generationDate->appendChild($document->createTextNode($objDateTime->format('c')));
//Получим список агентов 
$ag_table_query="SELECT * FROM gcn_kadr WHERE id_role = 3 AND agent_is_active = 1 ORDER BY agent_fio ASC, agent_is_active asc; " ;
$result_ag_table=$db->query($ag_table_query);
if (!$result_ag_table) {
        echo "Невозможно выполнить запрос ($ag_table_query) из БД: " . mysql_error();
        exit;
    }
if ($result_ag_table->num_rows == 0) {
        echo "Запрос к списку агентов вернул 0 строк, выполнение прервано!";
        exit;
    }
//помещаем в массив
    while ($row_agents = $result_ag_table->fetch_assoc())
    {
		//добавление корневого эл-та offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//добавление аттрибута type со значением "agent" к offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'agent';
		$offer->appendChild($offerAttributeType);
		//добавление аттрибута id с идентификатором из нашей БД
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_agents['id_agent'];
		$offer->appendChild($offerAttributeId);
		//Добавление дочерних эл-тов в offer:
		//Краткое ФИО
		$fio = $offer->appendChild($document->createElement('fio'));
		$fio->appendChild($document->createTextNode($row_agents['agent_fio']));
		//$Фамилия
		$surname = $offer->appendChild($document->createElement('surname'));
		$surname->appendChild($document->createTextNode($row_agents['lastname']));
		//Имя
		$name = $offer->appendChild($document->createElement('name'));
		$name->appendChild($document->createTextNode($row_agents['name']));
		//Отчество
		$secondName = $offer->appendChild($document->createElement('second-name'));
		$secondName->appendChild($document->createTextNode($row_agents['otchestvo']));
		//Логин
	
		$login = $offer->appendChild($document->createElement('login'));
		$login->appendChild($document->createTextNode($row_agents['agent_login']));
		//Пароль
		$password = $offer->appendChild($document->createElement('password'));
		$password->appendChild($document->createTextNode('Kapusta8'));
		
		//телефон для публикации
		$phone = $offer->appendChild($document->createElement('phone'));
		$phone->appendChild($document->createTextNode($row_agents['phone']));
		//емэйл
			if($row_agents['email_api']){
			$email = $offer->appendChild($document->createElement('email'));
			$email->appendChild($document->createTextNode($row_agents['email_api'] . $email_domain));
			}
			
			else{
			$email = $offer->appendChild($document->createElement('email'));
			$email->appendChild($document->createTextNode($row_agents['email1']));
		
			}
			
		//уволен или нет - 1/0
		$dismiss = $offer->appendChild($document->createElement('dismiss'));
		$dismiss->appendChild($document->createTextNode(($row_agents['agent_is_active']=="1"? "0": "1")));
		
	}
		$result_ag_table->free();
		
		/*
			Формируем квартиры
		
		*/
		$flats_table_query="SELECT * FROM gcn_flats, gcn_flats_ext WHERE  gcn_flats_ext.adv_for_emls =1 AND gcn_flats.id = gcn_flats_ext.id AND objects_status = 0  AND removal_request = '1';";
		$result_flats_table = $db->query($flats_table_query);
		if (!$result_flats_table) {
        echo "Невозможно выполнить запрос ($ag_table_query) из БД: " . mysql_error();
        exit;
    }
		if ($result_flats_table->num_rows == 0) {
        echo "Запрос к списку агентов вернул 0 строк, выполнение прервано!";
        exit;
		}		
		
		//помещаем в массив
		while($row_flats = $result_flats_table->fetch_assoc())
		{
			
			//Если есть ИД клиента, получаем детальную информацию о клиенте
			
			if(!empty($row_flats['id_client'])){
				
				echo $row_flats['id_client'];
				
				$info_client = "SELECT * FROM `gcn_clients` WHERE `id`=".$row_flats['id_client'];
				$result_client = $db->query($info_client);
				
				//echo $info_client;
				
				if(!$result_client){
					echo "Невозможно выполнить запрос ($ag_table_query) из БД: " . mysql_error();
					exit();
				}
				if($result_client){
					$res_client = $result_client->fetch_assoc();
					//var_dump($res_client);
				}
			}
			
			//добавление корневого эл-та offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//добавление аттрибута id с идентификатором объекта из нашей БД
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_flats['id'];
		$offer->appendChild($offerAttributeId);
		//добавление аттрибута type со значением "flat" к offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'flat';
		$offer->appendChild($offerAttributeType);

		//Добавление дочерних эл-тов в offer:
		//Дата добавления объекта в базу
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_flats['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//дата обновления
		if($row_flats['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_flats['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		/*
				тег <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//регион
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_flats['id_reg']));
				//район
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_flats['id_dept']));
				//населенный пункт	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_flats['id_district']));
				//улица
					if($row_flats['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_flats['id_street']));}
				//номер дома
					if($row_flats['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_flats['house_number']));}
				//номер корпуса
					if($row_flats['house_korpus']){
					$houseСase = $location->appendChild($document->createElement('house-case'));
					$houseСase->appendChild($document->createTextNode($row_flats['house_korpus']));}
				//литера	
					if($row_flats['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_flats['house_letter']));}
				//станция метро
					if($row_flats['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//костыль
					//метро девяткино
                        if($row_flats['id_metro']=='350025') {
                            $row_flats['id_metro'] = '22';
                        }
                     //метро беговая   
                        if($row_flats['id_metro']=='350053') {
                            $row_flats['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_flats['id_metro']));
					}
				//как добраться
					if($row_flats['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_flats['id_metro_transport']));}
				//тег <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id агента тз БД
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_flats['id_user']));
				
				//кол-во комнат участвующих в сделке
				$rooms = $offer->appendChild($document->createElement('rooms'));
				
				if($row_flats['id_flat_type'] == 40){
					$row_flats['amount_rooms'] = 1;
				}				
				$rooms->appendChild($document->createTextNode($row_flats['amount_rooms']));
				//продажа доли
				$share_marker = $offer->appendChild($document->createElement('share-marker'));
				$share_marker->appendChild($document->createTextNode($row_flats['is_part']));
				//	планировка комнат, значение из справочника
				$roomsType = $offer->appendChild($document->createElement('rooms-type'));
				$roomsType->appendChild($document->createTextNode($row_flats['id_flat_type']));
				//Общая площадь всей квартиры  м.кв.
				$allSpace = $offer->appendChild($document->createElement('all-space'));
				$allSpace->appendChild($document->createTextNode($row_flats['s_all']));
				//жилая площадь м.кв.
				$livingSpace = $offer->appendChild($document->createElement('living-space'));
				$livingSpace->appendChild($document->createTextNode($row_flats['s_life']));
				//площадь кухни м.кв.
				if($row_flats['id_flat_type'] != 40){
					$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
					$kitchenSpace->appendChild($document->createTextNode($row_flats['s_kitchen']));
				}				
				//	Разбивка по площадям комнат 
				$living = $offer->appendChild($document->createElement('living'));
				$living->appendChild($document->createTextNode($row_flats['s_rooms']));
				//	площадь коридора м.кв.
				if($row_flats['s_corridor']){
				$corridorSpace = $offer->appendChild($document->createElement('corridor-space'));
				$corridorSpace->appendChild($document->createTextNode($row_flats['s_corridor']));}
				//площадь прихожей м.кв.
				if($row_flats['s_vestibule']){
				$hallSpace = $offer->appendChild($document->createElement('hall-space'));
				$hallSpace->appendChild($document->createTextNode($row_flats['s_vestibule']));}
				//высота потолка в метрах
				if($row_flats['s_ceiling']){
				$ceiling = $offer->appendChild($document->createElement('ceiling'));
				$ceiling->appendChild($document->createTextNode($row_flats['s_ceiling']));
				}
				//тип собственности, значение из справочника
				if($row_flats['id_type_property']){
				$propertyType = $offer->appendChild($document->createElement('property-type'));
				$propertyType->appendChild($document->createTextNode($row_flats['id_type_property']));
				}
				//	кол-во ордеров, если существенно для сделки. ???
				
				// 	кол-во жильцов, если существенно для сделки
				if($row_flats['tenants']){
				$tenants = $offer->appendChild($document->createElement('tenants'));
				$tenants->appendChild($document->createTextNode($row_flats['tenants']));
				}
				//	кол-во детей, если существенно для сделки
				if($row_flats['children']){
				$children = $offer->appendChild($document->createElement('children'));
				$children->appendChild($document->createTextNode($row_flats['children']));				
				}
				//	наличие балкона, справочник
				if($row_flats['id_balcony']){
				$balcony = $offer->appendChild($document->createElement('balcony'));
				$balcony->appendChild($document->createTextNode($row_flats['id_balcony']));	
				}
				// вид из окон, справочник
				if($row_flats['id_view_from_window']){
				$windowView = $offer->appendChild($document->createElement('window-view'));
				$windowView->appendChild($document->createTextNode($row_flats['id_view_from_window']));
				}
				// состояние квартиры, справочник
				if($row_flats['id_repair']){
				$quality = $offer->appendChild($document->createElement('quality'));
				$quality->appendChild($document->createTextNode(getQuality($row_flats['id_repair'])));
				}
				//	наличие телефона, справочник
				if($row_flats['id_phone']){
				$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
				$phoneSupply->appendChild($document->createTextNode($row_flats['id_phone']));
				}
				//тип санузла, справочник	
				if($row_flats['id_bathroom']){
				$bathroom = $offer->appendChild($document->createElement('bathroom'));
				$bathroom->appendChild($document->createTextNode($row_flats['id_bathroom']));
				}
				//тип ванны, справочник
				if($row_flats['id_bath']){
				$bath = $offer->appendChild($document->createElement('bath'));
				$bath->appendChild($document->createTextNode($row_flats['id_bath']));
				}
				//	обеспечение горячей водой, справочник
				if($row_flats['id_hot_water']){
				$hotwaterSupply = $offer->appendChild($document->createElement('hotwater-supply'));
				$hotwaterSupply->appendChild($document->createTextNode($row_flats['id_hot_water']));
				}
				//основное покрытие пола, справочник
				if($row_flats['id_floor_material']){
				$floorCovering = $offer->appendChild($document->createElement('floor-covering'));
				$floorCovering->appendChild($document->createTextNode($row_flats['id_floor_material']));
				}
				//Описание, 	текст до 8000 симв.
				if($row_flats['comment_for_clients']){
				$description = $offer->appendChild($document->createElement('description'));
				$description->appendChild($document->createTextNode($row_flats['comment_for_clients']));
				}
				// этаж объекта	
				$floor = $offer->appendChild($document->createElement('floor'));
				$floor->appendChild($document->createTextNode($row_flats['floor']));
				// 	этажность здания
				$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
				$floorsTotal->appendChild($document->createTextNode($row_flats['floor_all']));
				//тип дома, справочник
				$buildingType = $offer->appendChild($document->createElement('building-type'));
				$buildingType->appendChild($document->createTextNode($row_flats['id_type_house']));
				// Год постройки
				if($row_flats['year_build']){
				$builtYear = $offer->appendChild($document->createElement('built-year'));
				$builtYear->appendChild($document->createTextNode($row_flats['year_build']));
				}
				// год капремонта
				if($row_flats['year_capital_repair']){
				$repairYear = $offer->appendChild($document->createElement('repair-year'));
				$repairYear->appendChild($document->createTextNode($row_flats['year_capital_repair']));
				}
				
				//вход в дом, справочник
				if($row_flats['id_type_enter']){
				$buildingInput = $offer->appendChild($document->createElement('building-input'));
				$buildingInput->appendChild($document->createTextNode($row_flats['id_type_enter']));
				}
				//	мусоропровод, справочник
				if($row_flats['id_refuse_chute']){
				$rubbishChute = $offer->appendChild($document->createElement('rubbish-chute'));
				$rubbishChute->appendChild($document->createTextNode($row_flats['id_refuse_chute']));}
				// наличие лифта, справочник
				if($row_flats['id_lift']){
				$lift = $offer->appendChild($document->createElement('lift'));
				$lift->appendChild($document->createTextNode($row_flats['id_lift']));}
				// высота первого этажа, м
				if($row_flats['floor_first_height']){
				$groundfloorHeight = $offer->appendChild($document->createElement('groundfloor-height'));
				$groundfloorHeight->appendChild($document->createTextNode($row_flats['floor_first_height']));}
				//цена в рублях
				$priceRub = $row_flats['price']*1000;
				$price = $offer->appendChild($document->createElement('price'));
				$price->appendChild($document->createTextNode($priceRub));
				//тип сделки, справочник
				if($row_flats['id_type_flat']){
					
				echo 111;
				
				$dealType = $offer->appendChild($document->createElement('deal-type'));
				$dealType->appendChild($document->createTextNode($row_flats['id_type_flat']));
				
					if($row_flats['id_type_flat'] == 9){				

						$building_id = $offer->appendChild($document->createElement('building-id'));
						$building_id->appendChild($document->createTextNode($row_flats['building-id']));													
						
						$investor_first_name = $offer->appendChild($document->createElement('investor-first-name'));
						$investor_first_name->appendChild($document->createTextNode($res_client['client_fio_name']));	

						$investor_last_name = $offer->appendChild($document->createElement('investor-last-name'));
						$investor_last_name->appendChild($document->createTextNode($res_client['client_fio_family']));
						
						$investor_second_name = $offer->appendChild($document->createElement('investor-second-name'));
						$investor_second_name->appendChild($document->createTextNode($res_client['client_fio_surname']));
					}
				}
				//примечание к цене при расселении (мах 25 симв.)
				if($row_flats['formula_rasselenia']){
				$priceNote = $offer->appendChild($document->createElement('price-note'));
				$priceNote->appendChild($document->createTextNode($row_flats['formula_rasselenia']));
				}
				//возможность продажи по ипотеке («1»/«0»)
				if($row_flats['hypothec']){
				$mortgage = $offer->appendChild($document->createElement('mortgage'));
				$mortgage->appendChild($document->createTextNode($row_flats['hypothec']));
				}
                //апартаменты («1»/«0»)
                if($row_flats['apartment']) {
                    $apartment = $offer->appendChild($document->createElement('apartment'));
                    $apartment->appendChild($document->createTextNode($row_flats['apartment']));
            }
                //пентхаус («1»/«0»)
                if($row_flats['penthouse']) {
                $penthouse = $offer->appendChild($document->createElement('penthouse'));
                $penthouse->appendChild($document->createTextNode($row_flats['penthouse']));
            }
				//парковка
                if($row_flats['id_parking']) {
                $parking = $offer->appendChild($document->createElement('parking'));
                $parking->appendChild($document->createTextNode($row_flats['id_parking']));
            }
				//пандус
				if($row_flats['ramp']) {
                $ramp = $offer->appendChild($document->createElement('ramp'));
                $ramp->appendChild($document->createTextNode($row_flats['ramp']));
            }
				//числитель доли, если продажа доли
				if($row_flats['part_numerator']){
				$part_numerator = $offer->appendChild($document->createElement('part-numerator'));
				$part_numerator->appendChild($document->createTextNode($row_flats['part_numerator']));
				}
				//знаменатель доли, если продажа доли
				if($row_flats['part_denominator']){
				$part_denominator = $offer->appendChild($document->createElement('part-denominator'));
				$part_denominator->appendChild($document->createTextNode($row_flats['part-denominator']));
				}
				//аукцион
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_flats['auction_marker']));
				//тип плиты
				if($row_flats['id_stove_plate']){
                $stove = $offer->appendChild($document->createElement('stove'));
                $stove->appendChild($document->createTextNode($row_flats['id_stove_plate']));
            }
				//ссылка на видео
				if($row_flats['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_flats['video'])));
				}
				//Фотографии
				$id_flat_image=$row_flats['id'];
				//получим список фоток запросом, id_base =1 --->квартиры
					$flats_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_flat_image AND id_base = 1 AND photo_status = 1";
					$result_flats_images= $db->query($flats_images_emls_query);
					//выводим,если есть хоть одна фотка
					if($result_flats_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_flats_foto = $result_flats_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto'.$row_flats_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//Сортировка
							if($row_flats_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_flats_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_flats_foto['photo_sorting']));}				
							//Описание
							if($row_flats_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_flats_foto['photo_comment']));
							}					
					}
				}
				$result_flats_images->free();
	
				
				
		}	
						//закончили с квартирами
				$result_flats_table->free();
				unset($row_flats);
				
				//далее дописываем комнаты
				
				
				$rooms_table_query="SELECT * FROM gcn_rooms, gcn_rooms_ext WHERE  gcn_rooms_ext.adv_for_emls =1 AND gcn_rooms.id = gcn_rooms_ext.id AND objects_status = 0 AND removal_request = '1';";
					$result_rooms_table = $db->query($rooms_table_query);
						if (!$result_rooms_table) {
							echo "Невозможно выполнить запрос ($rooms_table_query) из БД: " . mysql_error();
							exit;
					}
					if ($result_rooms_table->num_rows == 0) {
						echo "Запрос к списку агентов вернул 0 строк, выполнение прервано!";
							exit;
							}
				
				//помещаем в массив
		while($row_flats = $result_rooms_table->fetch_assoc())
		{
			//добавление корневого эл-та offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//добавление аттрибута id с идентификатором объекта из нашей БД
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_flats['id'];
		$offer->appendChild($offerAttributeId);
		//добавление аттрибута type со значением "room" к offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'room';
		$offer->appendChild($offerAttributeType);
		
		//Добавление дочерних эл-тов в offer:
		//Дата добавления объекта в базу
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_flats['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//дата обновления
		if($row_flats['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_flats['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		
		/*
				тег <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//регион
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_flats['id_reg']));
				//район
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_flats['id_dept']));
				//населенный пункт	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_flats['id_district']));
				//улица
					if($row_flats['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_flats['id_street']));}
				//номер дома
					if($row_flats['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_flats['house_number']));}
				//номер корпуса
					if($row_flats['house_korpus']){
					$houseСase = $location->appendChild($document->createElement('house-case'));
					$houseСase->appendChild($document->createTextNode($row_flats['house_korpus']));}
				//литера	
					if($row_flats['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_flats['house_letter']));}
				//станция метро
					if($row_flats['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//костыль
					//метро девяткино
                        if($row_flats['id_metro']=='350025') {
                            $row_flats['id_metro'] = '22';
                        }
                     //метро беговая   
                        if($row_flats['id_metro']=='350053') {
                            $row_flats['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_flats['id_metro']));
					}
				//как добраться
					if($row_flats['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_flats['id_metro_transport']));}
		
		
		
			//тег <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id агента тз БД
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_flats['id_user']));
		
				//кол-во комнат участвующих в сделке
				$rooms = $offer->appendChild($document->createElement('rooms'));
				$rooms->appendChild($document->createTextNode($row_flats['amount_rooms']));
				//продажа доли???
			
				//	планировка комнат, значение из справочника
				$roomsType = $offer->appendChild($document->createElement('rooms-type'));
				$roomsType->appendChild($document->createTextNode($row_flats['id_flat_type']));
				//Общая площадь всей квартиры  м.кв.
				$allSpace = $offer->appendChild($document->createElement('all-space'));
				$allSpace->appendChild($document->createTextNode($row_flats['s_all']));
				//продаваемая площадь м.кв.
				$livingSpace = $offer->appendChild($document->createElement('living-space'));
				$livingSpace->appendChild($document->createTextNode($row_flats['s_life']));
				//площадь кухни м.кв.
				$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
				$kitchenSpace->appendChild($document->createTextNode($row_flats['s_kitchen']));
				//	Разбивка по площадям комнат 
				$living = $offer->appendChild($document->createElement('living'));
				$living->appendChild($document->createTextNode($row_flats['s_rooms']));
				//	площадь коридора м.кв.
				if($row_flats['s_corridor']){
				$corridorSpace = $offer->appendChild($document->createElement('corridor-space'));
				$corridorSpace->appendChild($document->createTextNode($row_flats['s_corridor']));}
				//площадь прихожей м.кв.
				if($row_flats['s_vestibule']){
				$hallSpace = $offer->appendChild($document->createElement('hall-space'));
				$hallSpace->appendChild($document->createTextNode($row_flats['s_vestibule']));}
				//высота потолка в метрах
				if($row_flats['s_ceiling']){
				$ceiling = $offer->appendChild($document->createElement('ceiling'));
				$ceiling->appendChild($document->createTextNode($row_flats['s_ceiling']));
				}
				//тип собственности, значение из справочника
				if($row_flats['id_type_property']){
				$propertyType = $offer->appendChild($document->createElement('property-type'));
				$propertyType->appendChild($document->createTextNode($row_flats['id_type_property']));
				}
				//	кол-во ордеров, если существенно для сделки. ???
				
				// 	кол-во жильцов, если существенно для сделки
				if($row_flats['tenants']){
				$tenants = $offer->appendChild($document->createElement('tenants'));
				$tenants->appendChild($document->createTextNode($row_flats['tenants']));
				}
				//	кол-во детей, если существенно для сделки
				if($row_flats['children']){
				$children = $offer->appendChild($document->createElement('children'));
				$children->appendChild($document->createTextNode($row_flats['children']));				
				}
				//	наличие балкона, справочник
				if($row_flats['id_balcony']){
				$balcony = $offer->appendChild($document->createElement('balcony'));
				$balcony->appendChild($document->createTextNode($row_flats['id_balcony']));	
				}
				// вид из окон, справочник
				if($row_flats['id_view_from_window']){
				$windowView = $offer->appendChild($document->createElement('window-view'));
				$windowView->appendChild($document->createTextNode($row_flats['id_view_from_window']));
				}
				// состояние квартиры, справочник
				if($row_flats['id_repair']){
				$quality = $offer->appendChild($document->createElement('quality'));
				$quality->appendChild($document->createTextNode(getQuality($row_flats['id_repair'])));
				}
				//	наличие телефона, справочник
				if($row_flats['id_phone']){
				$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
				$phoneSupply->appendChild($document->createTextNode($row_flats['id_phone']));
				}
				//тип санузла, справочник	
				if($row_flats['id_bathroom']){
				$bathroom = $offer->appendChild($document->createElement('bathroom'));
				$bathroom->appendChild($document->createTextNode($row_flats['id_bathroom']));
				}
				//тип ванны, справочник
				if($row_flats['id_bath']){
				$bath = $offer->appendChild($document->createElement('bath'));
				$bath->appendChild($document->createTextNode($row_flats['id_bath']));
				}
				//	обеспечение горячей водой, справочник
				if($row_flats['id_hot_water']){
				$hotwaterSupply = $offer->appendChild($document->createElement('hotwater-supply'));
				$hotwaterSupply->appendChild($document->createTextNode($row_flats['id_hot_water']));
				}
				//основное покрытие пола, справочник
				if($row_flats['id_floor_material']){
				$floorCovering = $offer->appendChild($document->createElement('floor-covering'));
				$floorCovering->appendChild($document->createTextNode($row_flats['id_floor_material']));
				}
				//Описание, 	текст до 8000 симв.
				if($row_flats['comment_for_clients']){
				$description = $offer->appendChild($document->createElement('description'));
				$description->appendChild($document->createTextNode($row_flats['comment_for_clients']));
				}
				// этаж объекта	
				$floor = $offer->appendChild($document->createElement('floor'));
				$floor->appendChild($document->createTextNode($row_flats['floor']));
				// 	этажность здания
				$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
				$floorsTotal->appendChild($document->createTextNode($row_flats['floor_all']));
				//тип дома, справочник
				$buildingType = $offer->appendChild($document->createElement('building-type'));
				$buildingType->appendChild($document->createTextNode($row_flats['id_type_house']));
				// Год постройки
				if($row_flats['year_build']){
				$builtYear = $offer->appendChild($document->createElement('built-year'));
				$builtYear->appendChild($document->createTextNode($row_flats['year_build']));
				}
				// год капремонта
				if($row_flats['year_capital_repair']){
				$repairYear = $offer->appendChild($document->createElement('repair-year'));
				$repairYear->appendChild($document->createTextNode($row_flats['year_capital_repair']));
				}
				
				//вход в дом, справочник
				if($row_flats['id_type_enter']){
				$buildingInput = $offer->appendChild($document->createElement('building-input'));
				$buildingInput->appendChild($document->createTextNode($row_flats['id_type_enter']));
				}
				//	мусоропровод, справочник
				if($row_flats['id_refuse_chute']){
				$rubbishChute = $offer->appendChild($document->createElement('rubbish-chute'));
				$rubbishChute->appendChild($document->createTextNode($row_flats['id_refuse_chute']));}
								//парковка
                if($row_flats['id_parking']) {
                $parking = $offer->appendChild($document->createElement('parking'));
                $parking->appendChild($document->createTextNode($row_flats['id_parking']));
            }
				// наличие лифта, справочник
				if($row_flats['id_lift']){
				$lift = $offer->appendChild($document->createElement('lift'));
				$lift->appendChild($document->createTextNode($row_flats['id_lift']));}
				// высота первого этажа, м
				if($row_flats['floor_first_height']){
				$groundfloorHeight = $offer->appendChild($document->createElement('groundfloor-height'));
				$groundfloorHeight->appendChild($document->createTextNode($row_flats['floor_first_height']));}
				//цена в рублях
				$priceRub = $row_flats['price']*1000;
				$price = $offer->appendChild($document->createElement('price'));
				$price->appendChild($document->createTextNode($priceRub));
				//тип сделки, справочник
				if($row_flats['id_type_flat']){
				$dealType = $offer->appendChild($document->createElement('deal-type'));
				$dealType->appendChild($document->createTextNode($row_flats['id_type_flat']));}
				//примечание к цене при расселении (мах 25 симв.)
				if($row_flats['formula_rasselenia']){
				$priceNote = $offer->appendChild($document->createElement('price-note'));
				$priceNote->appendChild($document->createTextNode($row_flats['formula_rasselenia']));
				}
				//возможность продажи по ипотеке («1»/«0»)
				if($row_flats['hypothec']){
				$mortgage = $offer->appendChild($document->createElement('mortgage'));
				$mortgage->appendChild($document->createTextNode($row_flats['hypothec']));
				}
				//Общее кол-во комнат, только « продажа комнат».
				if($row_flats['amount_rooms_total']){
				$roomsTotal = $offer->appendChild($document->createElement('rooms-total'));
				$roomsTotal->appendChild($document->createTextNode($row_flats['amount_rooms_total']));
				}
				//аукцион
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_flats['auction_marker']));
				//пандус
				if($row_flats['ramp']) {
                $ramp = $offer->appendChild($document->createElement('ramp'));
                $ramp->appendChild($document->createTextNode($row_flats['ramp']));
				}
				//тип плиты
				if($row_flats['id_stove_plate']) {
                $stove = $offer->appendChild($document->createElement('stove'));
                $stove->appendChild($document->createTextNode($row_flats['id_stove_plate']));
				}
				//ссылка на видео
				if($row_flats['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_flats['video'])));
				}
						//Фотографии
				$id_flat_image=$row_flats['id'];
				//получим список фоток запросом, id_base =2 --->комнаты
					$flats_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_flat_image AND id_base = 2 AND photo_status = 1";
					$result_flats_images= $db->query($flats_images_emls_query);
					//выводим,если есть хоть одна фотка
					if($result_flats_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_flats_foto = $result_flats_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_flats_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//Сортировка
							if($row_flats_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_flats_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_flats_foto['photo_sorting']));}
							//Описание
							if($row_flats_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_flats_foto['photo_comment']));
							}					
					}
				}
				$result_flats_images->free();
		
		
		}
						//закончили с комнатами
				$result_rooms_table->free();
				unset($row_flats);		
				//делаем коммерцию
				
				$commer_table_query="SELECT * FROM gcn_comm, gcn_comm_ext WHERE  gcn_comm_ext.adv_for_emls =1 AND gcn_comm.id = gcn_comm_ext.id AND objects_status = 0  AND removal_request = '1' LIMIT 0,600;";
					$result_commer_table = $db->query($commer_table_query);
					if (!$result_commer_table) {
					echo "Невозможно выполнить запрос ($commer_table_query) из БД: " . mysql_error();
					exit;
						}
					if ($result_commer_table->num_rows == 0) {
					echo "Запрос к списку объектов вернул 0 строк, выполнение прервано!";
					exit;
						}
				
						//помещаем в массив
		while($row_commer = $result_commer_table->fetch_assoc())
		{
			//добавление корневого эл-та offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//добавление аттрибута id с идентификатором объекта из нашей БД
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_commer['id'];
		$offer->appendChild($offerAttributeId);
		//добавление аттрибута type со значением "commerce" к offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'commerce';
		$offer->appendChild($offerAttributeType);

		//Добавление дочерних эл-тов в offer:
		
		//Дата добавления объекта в базу
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_commer['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//дата обновления
		if($row_commer['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_commer['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		/*
				тег <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//регион
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_commer['id_reg']));
				//район
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_commer['id_dept']));
				//населенный пункт	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_commer['id_district']));
				//улица
				if($row_commer['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_commer['id_street']));}
				//номер дома
					if($row_commer['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_commer['house_number']));}
				//номер корпуса
					if($row_commer['house_korpus_fact']){
					$houseCase = $location->appendChild($document->createElement('house-case'));
					$houseCase->appendChild($document->createTextNode($row_commer['house_korpus_fact']));}
				//литера	
					if($row_commer['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_commer['house_letter']));}
				//станция метро
					if($row_commer['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//костыль
					//метро девяткино
                        if($row_commer['id_metro']=='350025') {
                            $row_commer['id_metro'] = '22';
                        }
                     //метро беговая   
                        if($row_commer['id_metro']=='350053') {
                            $row_commer['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_commer['id_metro']));
					}
				//как добраться
					if($row_commer['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_commer['id_metro_transport']));}
		
				//тег <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id агента из БД
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_commer['id_user']));
					
				//	вид объекта, справочник
					$category = $offer->appendChild($document->createElement('category'));
					
					$category->appendChild($document->createTextNode(get_emls_type_property($row_commer['id_type'])));
					
				//тип здания,обязательно для здания/помещения
				if($row_commer['id_type'] != 5){
					if($row_commer['id_type'] != 6){
					$buildingType = $offer->appendChild($document->createElement('building-type'));
					$buildingType->appendChild($document->createTextNode('3'));
					}
					else{
					$buildingType = $offer->appendChild($document->createElement('building-type'));
					$buildingType->appendChild($document->createTextNode('2'));
					}
				}
				//статус встроенного помещения,обязательно для помещения 
				//1 -жилой фонд 2-нежилой фонд, в любой случае нежилой
				if($row_commer['id_type'] == 6 || $row_commer['id_type'] == 7){
					$subcategory = $offer->appendChild($document->createElement('subcategory'));
					$subcategory->appendChild($document->createTextNode('2'));
					
				}
				
				//Проверяем возможность продажи объект по частям
				
				if(!empty($row_commer['space_min'])){
					$all_space_min = $offer->appendChild($document->createElement('all-space-min'));
					$all_space_min->appendChild($document->createTextNode($row_commer['space_min']));					
				}
				
				//	юридический статус
					$propertyType = $offer->appendChild($document->createElement('property-type'));
					$propertyType->appendChild($document->createTextNode($row_commer['id_property']));
				//общая площадь, м.кв.
					$allSpace = $offer->appendChild($document->createElement('all-space'));
					$allSpace->appendChild($document->createTextNode($row_commer['s_all']));
				//возможные назначения, перечислимое, справочник
					
					//Содержит один или более вложенных тегов <value> 
					//Будем разбивать с помощью  explode список назначений на отдельные тэги <value>,"," является разделителем,далее получаем НЕПОВТОРЯЮЩИЕСЯ значения emls из таблицы gcn_list_comm_use
				getIdEmlsUse();
				//вход в объект, справочник
				if($row_commer['id_entry']){
					$buildingInput = $offer->appendChild($document->createElement('building-input'));
					$buildingInput->appendChild($document->createTextNode($row_commer['id_entry']));}
					//$buildingInput->appendChild($document->createTextNode(getIdEmlsUse()."count"));}
		
					
				//этажи свободный текст (мах 50 симв.)
				if($row_commer['floors_text']){
					$floor = $offer->appendChild($document->createElement('floor'));
					$floor->appendChild($document->createTextNode($row_commer['floors_text']));
				}
				//этажность здания
				if($row_commer['floors']){
					$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
					$floorsTotal->appendChild($document->createTextNode($row_commer['floors']));
				}
				//	состояние помещения, справочник.
				if($row_commer['id_cont']){
					$quality = $offer->appendChild($document->createElement('quality'));
					$quality->appendChild($document->createTextNode($row_commer['id_cont']));
				}
				//водоснабжение , «1»/«0»
				if($row_commer['id_water']){
					$waterSupply = $offer->appendChild($document->createElement('water-supply'));
					$waterSupply->appendChild($document->createTextNode(($row_commer['id_water']=="2"?"1":"0")));
				}
				//канализация , «1»/«0»
				if($row_commer['id_sewer']){
					$sewerageSupply = $offer->appendChild($document->createElement('sewerage-supply'));
					$sewerageSupply->appendChild($document->createTextNode(($row_commer['id_sewer']=="2"?"1":"0")));
				}
				//теплоснабжение, «1»/«0»
					if($row_commer['id_heat']){
						$heatingSupply = $offer->appendChild($document->createElement('heating-supply'));
						$heatingSupply->appendChild($document->createTextNode(($row_commer['id_heat']=="2"?"1":"0")));
					}
				//электроснабжение, «1»/«0»
					if($row_commer['id_elec']){
						$electricitySupply = $offer->appendChild($document->createElement('electricity-supply'));
						$electricitySupply->appendChild($document->createTextNode(($row_commer['id_elec']=="2"?"1":"0")));
					}
				//телефонная линия, «1»/«0»
					if($row_commer['id_phone']){
						$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
						$phoneSupply->appendChild($document->createTextNode(($row_commer['id_phone']=="2"?"1":"0")));
					}
				//лифт, «1»/«0»
					if($row_commer['id_lift']){
						$lift = $offer->appendChild($document->createElement('lift'));
						$lift->appendChild($document->createTextNode(($row_commer['id_lift']=="2"?"1":"0")));
					}
				//Ж/Д пути, «1»/«0»
					if($row_commer['id_rroad']){
						$railway = $offer->appendChild($document->createElement('railway'));
						$railway->appendChild($document->createTextNode(($row_commer['id_rroad']=="2"?"1":"0")));
					}
				//подъездные пути, справочник	
					if($row_commer['id_access_road']){
						$accessRoad = $offer->appendChild($document->createElement('access-road'));
						$accessRoad->appendChild($document->createTextNode($row_commer['id_access_road']));
					}
				//площадь земельного участка, Га
					if($row_commer['area']){
					$lotArea = $offer->appendChild($document->createElement('lot-area'));
					$lotArea->appendChild($document->createTextNode($row_commer['area']/100));}
				//	срок аренды земли в годах
					if($row_commer['area_for_years']){
					$lotRentYear = $offer->appendChild($document->createElement('lot-rent-year'));
					$lotRentYear->appendChild($document->createTextNode($row_commer['area_for_years']));
					}
				//	собственность на земельный участок ,«1»/«0»
					$lotSale = $offer->appendChild($document->createElement('lot-sale'));
					$lotSale->appendChild($document->createTextNode(($row_commer['id_property_land']== "1"?"1":"0")));
				//	категория земель, справочник,обязательно для земли
					if($row_commer['id_type'] == 5){
						if(strpos($row_commer['str_postr'],'45')){
						$landCategory = $offer->appendChild($document->createElement('land-category'));
						$landCategory->appendChild($document->createTextNode('4'));
						}
						else if(strpos($row_commer['str_postr'],'46')){
						$landCategory = $offer->appendChild($document->createElement('land-category'));
						$landCategory->appendChild($document->createTextNode('3'));
						}
						else{
						$landCategory = $offer->appendChild($document->createElement('land-category'));
						$landCategory->appendChild($document->createTextNode('2'));
						}
					}
				//	подробное описание объекта, до 8000 сим.
					if($row_commer['comment_for_clients']){
					$description = $offer->appendChild($document->createElement('description'));
					$description->appendChild($document->createTextNode($row_commer['comment_for_clients']));
					}
				//цена продажи, руб.	
					if($row_commer['price_s'] and $row_commer['id_price_s']=="7"){
					$priceSale= $offer->appendChild($document->createElement('price-sale'));
					$priceSale->appendChild($document->createTextNode($row_commer['price_s']));
					}
					else if($row_commer['price_s']){
					$priceSale= $offer->appendChild($document->createElement('price-sale'));
					$priceSale->appendChild($document->createTextNode($row_commer['price_s']*1000));}
				//	единицы цены продажи
				/*
					Одно из значений:
					6 - указана цена за всё,
					7- указана цена за кв.м.
					
				*/
					if($row_commer['price_s'] and $row_commer['id_price_s']){
					$priceSaleUnit= $offer->appendChild($document->createElement('price-sale-unit'));
					$priceSaleUnit->appendChild($document->createTextNode(($row_commer['id_price_s']=="7"?"7":"6")));
					}
				//цена аренды, руб.
				//если стоит id_price_ar = 8(тыс руб) то переводим на рубли вместо тысяч
					if($row_commer['price_ar'] and $row_commer['id_price_ar']=="8"){
					$priceRent= $offer->appendChild($document->createElement('price-rent'));
					$priceRent->appendChild($document->createTextNode($row_commer['price_ar']*1000));
					}
					else if($row_commer['price_ar']){
					$priceRent= $offer->appendChild($document->createElement('price-rent'));
					$priceRent->appendChild($document->createTextNode($row_commer['price_ar']));
					}
				//	единицы цены аренды
				/*
					Одно из значений:
					2 - руб./м в мес.
					3 - руб./м в год.
					4 - руб. в мес. за всё
					В БД id не те совершенно,поэтому если если стоит "8"(тыс.руб. в месяц) то ставим 4,если все остальное -то ставим 2(руб./метр в месяц)
				*/
					if($row_commer['price_ar'] and $row_commer['id_price_ar']){
					$priceRent= $offer->appendChild($document->createElement('price-rent-unit'));
					$priceRent->appendChild($document->createTextNode(($row_commer['id_price_ar']=="8"?"4":"2")));
					}
				
				//цена ППА
					if($row_commer['price_ar_s']){
					$priceSaleRent= $offer->appendChild($document->createElement('price-salerent'));
					$priceSaleRent->appendChild($document->createTextNode($row_commer['price_ar_s']*1000));
					}
				//единицы цены ППА
				/*
					Одно из значений:
					8 - указана цена за всё,
					9 - указана цена за кв.м.
				*/
					if($row_commer['price_ar_s'] and $row_commer['id_price_ar_s']){
					$priceSaleRentUnit= $offer->appendChild($document->createElement('price-salerent-unit'));
					$priceSaleRentUnit->appendChild($document->createTextNode(($row_commer['id_price_ar_s']=="8"?"8":"9")));
					}
					
				//примечание к цене
					if($row_commer['price_comment']){
					$priceNote = $offer->appendChild($document->createElement('price-note'));
					$priceNote->appendChild($document->createTextNode($row_commer['price_comment']));
					}
				//размер комиссии агента, %
					$agentFee = $offer->appendChild($document->createElement('agent-fee'));
					$agentFee->appendChild($document->createTextNode($row_commer['agent_fee']));
					
				//предоплата (мес) (только для аренды),	обязательно для аренды
				if($row_commer['price_ar']){
					$rentAdvancePayment = $offer->appendChild($document->createElement('rent-advance-payment'));
					$rentAdvancePayment->appendChild($document->createTextNode('1'));
				}
					
				//возможность продажи по ипотеке
					if($row_commer['hypothec']){
					$mortgage = $offer->appendChild($document->createElement('mortgage'));
					$mortgage->appendChild($document->createTextNode($row_commer['hypothec']));					
					}
				//Гараж
				if($row_commer['garage']){
					$garage = $offer->appendChild($document->createElement('garage'));
					$garage->appendChild($document->createTextNode($row_commer['garage']));
				}
				//Тип гаража
				if($row_commer['garage_type']){
					$garage_type = $offer->appendChild($document->createElement('garage_type'));
					$garage_type->appendChild($document->createTextNode($row_commer['garage_type']));
				}
				//Тип материала гаража
				if($row_commer['garage_box_type']){
					$garage_box_type = $offer->appendChild($document->createElement('garage_box_type'));
					$garage_box_type->appendChild($document->createTextNode($row_commer['garage_box_type']));
				}
				//Статус собственности гаража
				if($row_commer['garage_status']){
					$garage_status = $offer->appendChild($document->createElement('garage_status'));
					$garage_status->appendChild($document->createTextNode($row_commer['garage_status']));
				}
				//аукцион
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_commer['auction_marker']));
				/*
				налогообложение
				<commercialvat>
				<id>2</id>
				<name>НДС включен</name>
				</commercialvat>
				<commercialvat>
				<id>3</id>
				<name>НДС не включен</name>
				</commercialvat>
				<commercialvat>
				<id>4</id>
				<name>УСН (упрощенная система налогообложения)</name>
				</commercialvat>

				*/
				$vat = $offer->appendChild($document->createElement('vat'));
				$vat->appendChild($document->createTextNode($row_commer['id_nalog']));

										//Фотографии
				$id_commer_image=$row_commer['id'];
				//получим список фоток запросом, id_base =5 --->коммерция
					$commer_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_commer_image AND id_base = 5 AND photo_status = 1";
					$result_commer_images= $db->query($commer_images_emls_query);
					//выводим,если есть хоть одна фотка
					if($result_commer_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_commer_foto = $result_commer_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_commer_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//Сортировка
							if($row_commer_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_commer_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_commer_foto['photo_sorting']));
							}
												
							//Описание
							if($row_commer_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_commer_foto['photo_comment']));
							}					
					}
				}
				$result_commer_images->free();

	}				
					
					
						
							
							
							//закончили с коммерцией
					$result_commer_table->free();
					unset($row_commer);	
				
								//делаем загородную недвижимость
				
				$farm_table_query="SELECT * FROM gcn_farm, gcn_farm_ext WHERE  gcn_farm_ext.adv_for_emls =1 AND gcn_farm.id = gcn_farm_ext.id AND objects_status = 0  AND removal_request = '1';";
					$result_farm_table = $db->query($farm_table_query);
					if (!$result_farm_table) {
					echo "Невозможно выполнить запрос ($farm_table_query) из БД: " . mysql_error();
					exit;
						}
					if ($result_farm_table->num_rows == 0) {
					echo "Запрос к списку объектов вернул 0 строк, выполнение прервано!";
					exit;
						}
						//помещаем в массив
						while($row_farm = $result_farm_table->fetch_assoc())
		{
							//добавление корневого эл-та offer
				$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
					//добавление аттрибута id с идентификатором объекта из нашей БД
					$offerAttributeId = $document->createAttribute('id');
					$offerAttributeId->value = $row_farm['id'];
					$offer->appendChild($offerAttributeId);
					//добавление аттрибута type со значением "country" к offer
					$offerAttributeType = $document->createAttribute('type');
					$offerAttributeType->value = 'country';
					$offer->appendChild($offerAttributeType);

					//Добавление дочерних эл-тов в offer:
					
						
					//Дата добавления объекта в базу
				$creationDate = $offer->appendChild($document->createElement('creation-date'));
					$cDate = date('Y-m-d', strtotime($row_farm['date_add']));
					$creationDate->appendChild($document->createTextNode($cDate));
					//дата обновления
			if($row_farm['date_edit']){
				$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
					$upDate = date('Y-m-d', strtotime($row_farm['date_update_emls']));
					$lastUpdateDate->appendChild($document->createTextNode($upDate));
				}	
					
				/*
				тег <location>
					*/
				$location = $offer->appendChild($document->createElement('location'));
				//регион
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_farm['id_reg']));
				//район
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_farm['id_dept']));
				//населенный пункт	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_farm['id_district']));
				//улица
				if($row_farm['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_farm['id_street']));}
				//номер дома
					if($row_farm['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_farm['house_number']));}
				//номер корпуса
					if($row_farm['house_korpus']){
					$houseСase = $location->appendChild($document->createElement('house-case'));
					$houseСase->appendChild($document->createTextNode($row_farm['house_korpus']));}
				//литера	
					if($row_farm['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_farm['house_letter']));}
				//станция метро
					if($row_farm['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//костыль
					//метро девяткино
                        if($row_farm['id_metro']=='350025') {
                            $row_farm['id_metro'] = '22';
                        }
                     //метро беговая   
                        if($row_farm['id_metro']=='350053') {
                            $row_farm['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_farm['id_metro']));
					}
				//как добраться
					if($row_farm['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_farm['id_metro_transport']));}
					
					
					//тег <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id агента из БД
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_farm['id_user']));
					
									//	вид объекта, справочник
					$category = $offer->appendChild($document->createElement('category'));
					$category->appendChild($document->createTextNode($row_farm['id_obj_type']));
									//тип собственности, справочник
					$propertyType = $offer->appendChild($document->createElement('property-type'));
					$propertyType->appendChild($document->createTextNode($row_farm['id_type_property']));
						//готовность дома, справочник	
					if($row_farm['id_ready']){
					$quality = $offer->appendChild($document->createElement('quality'));
					$quality->appendChild($document->createTextNode($row_farm['id_ready']));
				}
					//наличие приватизации, справочник	
					if($row_farm['id_proper']){
					$privatization = $offer->appendChild($document->createElement('privatization'));
					$privatization->appendChild($document->createTextNode($row_farm['id_proper']));}
					//готовность документов, справочник
					if($row_farm['id_ready_doc']){
					$documentReady = $offer->appendChild($document->createElement('document-ready'));
					$documentReady->appendChild($document->createTextNode($row_farm['id_ready_doc']));}
					//Удаленность от ж.д. станции (км)
					if($row_farm['km_from_station']){
					$distance = $offer->appendChild($document->createElement('distance'));
					$distance->appendChild($document->createTextNode($row_farm['km_from_station']));}
					//общественный транспорт, справочник
					if($row_farm['id_pod']){
					$transport = $offer->appendChild($document->createElement('transport'));
					$transport->appendChild($document->createTextNode($row_farm['id_pod']));}
					//подъездная дорога, справочник
					if($row_farm['id_road']){
					$road = $offer->appendChild($document->createElement('road'));
					$road->appendChild($document->createTextNode($row_farm['id_road']));}
					//наличие водоема, справочник
					if($row_farm['id_pond']){
					$pond = $offer->appendChild($document->createElement('pond'));
					$pond->appendChild($document->createTextNode($row_farm['id_pond']));}
					//варианты отопления, справочник
					if($row_farm['id_head']){
					$heatingSupply = $offer->appendChild($document->createElement('heating-supply'));
					$heatingSupply->appendChild($document->createTextNode($row_farm['id_head']));}
					//канализация, справочник
					if($row_farm['id_wc']){
					$toilet = $offer->appendChild($document->createElement('toilet'));
					$toilet->appendChild($document->createTextNode($row_farm['id_wc']));}
					//наличие телефона, справочник
					if($row_farm['id_phones']){
					$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
					$phoneSupply->appendChild($document->createTextNode($row_farm['id_phones']));}
					//электричество, справочник
					if($row_farm['id_electro']){
					$electricitySupply = $offer->appendChild($document->createElement('electricity-supply'));
					$electricitySupply->appendChild($document->createTextNode($row_farm['id_electro']));}
					//водоснабжение, справочник
					if($row_farm['id_water']){
					$waterSupply = $offer->appendChild($document->createElement('water-supply'));
					$waterSupply->appendChild($document->createTextNode($row_farm['id_water']));}
					//газоснабжение, справочник
					if($row_farm['id_gas']){
					$gasSupply = $offer->appendChild($document->createElement('gas-supply'));
					$gasSupply->appendChild($document->createTextNode($row_farm['id_gas']));}
					//площадь участка, сот.
					$lotArea = $offer->appendChild($document->createElement('lot-area'));
					$lotArea->appendChild($document->createTextNode($row_farm['area']));
					//состояние земельного участка, справочник
					if($row_farm['id_area_cond']){
					$lotCondition = $offer->appendChild($document->createElement('lot-condition'));
					$lotCondition->appendChild($document->createTextNode($row_farm['id_area_cond']));}
					//юридический статус земли, справочник
					if($row_farm['id_area_property']){
					$lotProperty = $offer->appendChild($document->createElement('lot-property'));
					$lotProperty->appendChild($document->createTextNode($row_farm['id_area_property']));}
					//этажей всего
					if($row_farm['floors']){
					$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
					$floorsTotal->appendChild($document->createTextNode($row_farm['floors']));}
					//габариты дома, м.6х8
					if($row_farm['gabar']){
						
						if(preg_match('/(x)(\*)/',$row_farm['gabar'],$matches, PREG_OFFSET_CAPTURE)!==false){
					$buildingSize = $offer->appendChild($document->createElement('building-size'));
					$buildingSize->appendChild($document->createTextNode($row_farm['gabar']));
					}
					else{
					$buildingSize = $offer->appendChild($document->createElement('building-size'));
					$buildingSize->appendChild($document->createTextNode(trim($row_farm['gabar']) . 'x' . trim($row_farm['gabar_ext'])));
						}
						
					}
					
					//количество комнат
					if($row_farm['amount_rooms']){
					$rooms = $offer->appendChild($document->createElement('rooms'));
					$rooms->appendChild($document->createTextNode($row_farm['amount_rooms']));}
					//общая площадь, м.кв. , только для дома.
					if($row_farm['s_total']){
					$allSpace = $offer->appendChild($document->createElement('all-space'));
					$allSpace->appendChild($document->createTextNode($row_farm['s_total']));}
					//жилая площадь, м.кв.
					if($row_farm['s_rooms']){
					$livingSpace = $offer->appendChild($document->createElement('living-space'));
					$livingSpace->appendChild($document->createTextNode($row_farm['s_rooms']));}
					//площадь кухни, м.кв.
					if($row_farm['s_kitchen']){
					$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
					$kitchenSpace->appendChild($document->createTextNode($row_farm['s_kitchen']));}
					//жилая площадь по комнатам
					if($row_farm['str_s_rooms']){
					$living = $offer->appendChild($document->createElement('living'));
					$living->appendChild($document->createTextNode($row_farm['str_s_rooms']));}
					//год постройки дома
					if($row_farm['year_build']){
					$builtYear = $offer->appendChild($document->createElement('built-year'));
					$builtYear->appendChild($document->createTextNode($row_farm['year_build']));}
					//материал стен дома, справочник
					if($row_farm['id_wall_mater']){
					$buildingType = $offer->appendChild($document->createElement('building-type'));
					$buildingType->appendChild($document->createTextNode($row_farm['id_wall_mater']));}
					//фундамент дома, справочник
					if($row_farm['id_funda']){
					$buildingBase = $offer->appendChild($document->createElement('building-base'));
					$buildingBase->appendChild($document->createTextNode($row_farm['id_funda']));}
					//крыша дома, справочник
					if($row_farm['id_roof']){
					$buildingRoof = $offer->appendChild($document->createElement('building-roof'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_roof']));}
					
					//документы
					if($row_farm['id_ready_doc']){
					$buildingRoof = $offer->appendChild($document->createElement('document-ready'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_ready_doc']));}
					//дорога
					if($row_farm['id_road']){
					$buildingRoof = $offer->appendChild($document->createElement('road'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_road']));}
					//	внутр.отделка, справочник
					if($row_farm['id_interior']){
					$interior = $offer->appendChild($document->createElement('interior'));
					$interior->appendChild($document->createTextNode($row_farm['id_interior']));}
					//дополнительные постройки, перечислимое, справочник
					//Содержит один или более вложенных тегов <value> со значениями из
					//http://emls.ru/api/v1.0/list/countrypostr ,попробуем реализовать как в коммерции
					if($row_farm['str_extra']){
					$additionalСonstructions = $offer->appendChild($document->createElement('additional-constructions'));
						$dop_value=explode(",",$row_farm['str_extra']);
						$i=0;
						while ($i<count($dop_value)){
							$emls_value = $dop_value[$i];
							//жестко указываем на тип integer,иначе баги
							settype($emls_value,"int");
							$value = $additionalСonstructions->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					//окружающий ландшафт, перечислимое, справочник
					if($row_farm['str_near']){
					$landscape = $offer->appendChild($document->createElement('landscape'));
						$near_value=explode(",",$row_farm['str_near']);
						$i=0;
						while ($i<count($near_value)){
							$emls_value = $near_value[$i];
							//жестко указываем на тип integer,иначе баги
							settype($emls_value,"int");
							$value = $landscape->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					
				//инфраструктура, перечислимое, справочник	
					if($row_farm['str_infra']){
						$infrastructure = $offer->appendChild($document->createElement('infrastructure'));
						$infra_value=explode(",",$row_farm['str_infra']);
						$i=0;
						while ($i<count($infra_value)){
							$emls_value = $infra_value[$i];
							//жестко указываем на тип integer,иначе баги
							settype($emls_value,"int");
							$value = $infrastructure->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					
				//дополнительные сведения неуказанные выше (до 8000 сим.).
					if($row_farm['comment_for_clients']){
					$description = $offer->appendChild($document->createElement('description'));
					$description->appendChild($document->createTextNode($row_farm['comment_for_clients']));}
				
				//цена, руб.
					$price = $offer->appendChild($document->createElement('price'));
					$price->appendChild($document->createTextNode($row_farm['price']*1000));
					
				//Доля от владения
					if($row_farm['fraction']){
						if(stripos($row_farm['fraction'],"/") > 0){
							$numerator = $offer->appendChild($document->createElement('part-numerator'));
							$trim_string = trim($row_farm['fraction']);
							$first = substr($trim_string,0,1);
							$last = substr($trim_string,-1);												
							$numerator->appendChild($document->createTextNode($first));	
							$denominator = $offer->appendChild($document->createElement('part-denominator'));						
							$denominator->appendChild($document->createTextNode($last));	
						}											
					}
					
				//	примечание к цене (мах 25 симв.)
					if($row_farm['price_comment']){
					$priceNote = $offer->appendChild($document->createElement('price-note'));
					$priceNote->appendChild($document->createTextNode($row_farm['price_comment']));}
				//возможность продажи по ипотеке
					if($row_farm['hypothec']){
					$mortgage = $offer->appendChild($document->createElement('mortgage'));
					$mortgage->appendChild($document->createTextNode($row_farm['hypothec']));}
					
				//аукцион
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_farm['auction_marker']));	
				
					//ссылка на видео
				if($row_farm['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_farm['video'])));
				}
															//Фотографии
				$id_farm_image=$row_farm['id'];
				//получим список фоток запросом, id_base =3 --->загород
					$farm_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_farm_image AND id_base = 3 AND photo_status = 1";
					$result_farm_images= $db->query($farm_images_emls_query);
					//выводим,если есть хоть одна фотка
					if($result_farm_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_farm_foto = $result_farm_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_farm_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//Сортировка
							if($row_farm_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_farm_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_farm_foto['photo_sorting']));
							}
												
							//Описание
							if($row_farm_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_farm_foto['photo_comment']));
							}					
					}
				}
				$result_farm_images->free();
					
						
		}
		unset($row_farm);
		$result_farm_table->free();
		
		
//генерация xml
$document->formatOutput = true; // установка атрибута formatOutput
                           // domDocument в значение true
						   

$test1 = $document->saveXML(); // передача строки в test1
//прикрутим расширяющийся лог
//дату и время запуска  будем записывать в начале
$currentTime=date('Y-m-d-H-i-s');
$fp = fopen('emls.log',"a+");
$log=fwrite($fp,$currentTime);
//передача строки в файл
$log=fwrite($fp,$test1);
$closeLog=fclose($fp);
if(!$log && !$closeLog){
	echo "Ошибка при записи либо закрытии файла";
}


$file = $document->save('/var/www/html/gcn/emls_xml/emls.xml'); // сохранение файла
if($file)
	echo "complete. \r\n ";
else echo "some error";
?>