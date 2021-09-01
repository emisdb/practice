<?php
//�������� ����� ���� ������
error_reporting(E_ALL);
//���������� �������
require_once('functions_converter_bkn_prof.php');
//������� ��������� ������ DomDocument
$document= new DomDocument ('1.0','utf-8');
//������� ��������� ������ mysqli
$db=new mysqli('localhost','gcn','gcn','gcn');
$db->set_charset('utf8');
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//������� ��������� ������  ��� ��������� ������� ���� �� ��������� ISO8601
$objDateTime = new DateTime('NOW');
/*
������ � �������� ������ 
*/
//�������� ������ ����������� �����
$email_domain="@gcn-spb.ru";
//���������� ��������� ��-�� emls-realty-feed
		$emls_realty_feed = $document->appendChild($document->createElement('emls-realty-feed'));
		//���������� ��������� type �� ��������� "agent" � offer
		$emls_realty_feedAttributeXmlns = $document->createAttribute('xmlns');
		$emls_realty_feedAttributeXmlns->value = 'http://emls.ru/schemas/2015-08';
		$emls_realty_feed->appendChild($emls_realty_feedAttributeXmlns);
//���������� 	��������� ��-�� generation-date-���� �������� ����	
		$generationDate = $emls_realty_feed->appendChild($document->createElement('generation-date'));
		$generationDate->appendChild($document->createTextNode($objDateTime->format('c')));
//������� ������ ������� 
$ag_table_query="SELECT * FROM gcn_kadr WHERE id_role = 3 AND agent_is_active = 1 ORDER BY agent_fio ASC, agent_is_active asc; " ;
$result_ag_table=$db->query($ag_table_query);
if (!$result_ag_table) {
        echo "���������� ��������� ������ ($ag_table_query) �� ��: " . mysql_error();
        exit;
    }
if ($result_ag_table->num_rows == 0) {
        echo "������ � ������ ������� ������ 0 �����, ���������� ��������!";
        exit;
    }
//�������� � ������
    while ($row_agents = $result_ag_table->fetch_assoc())
    {
		//���������� ��������� ��-�� offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//���������� ��������� type �� ��������� "agent" � offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'agent';
		$offer->appendChild($offerAttributeType);
		//���������� ��������� id � ��������������� �� ����� ��
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_agents['id_agent'];
		$offer->appendChild($offerAttributeId);
		//���������� �������� ��-��� � offer:
		//������� ���
		$fio = $offer->appendChild($document->createElement('fio'));
		$fio->appendChild($document->createTextNode($row_agents['agent_fio']));
		//$�������
		$surname = $offer->appendChild($document->createElement('surname'));
		$surname->appendChild($document->createTextNode($row_agents['lastname']));
		//���
		$name = $offer->appendChild($document->createElement('name'));
		$name->appendChild($document->createTextNode($row_agents['name']));
		//��������
		$secondName = $offer->appendChild($document->createElement('second-name'));
		$secondName->appendChild($document->createTextNode($row_agents['otchestvo']));
		//�����
	
		$login = $offer->appendChild($document->createElement('login'));
		$login->appendChild($document->createTextNode($row_agents['agent_login']));
		//������
		$password = $offer->appendChild($document->createElement('password'));
		$password->appendChild($document->createTextNode('Kapusta8'));
		
		//������� ��� ����������
		$phone = $offer->appendChild($document->createElement('phone'));
		$phone->appendChild($document->createTextNode($row_agents['phone']));
		//�����
			if($row_agents['email_api']){
			$email = $offer->appendChild($document->createElement('email'));
			$email->appendChild($document->createTextNode($row_agents['email_api'] . $email_domain));
			}
			
			else{
			$email = $offer->appendChild($document->createElement('email'));
			$email->appendChild($document->createTextNode($row_agents['email1']));
		
			}
			
		//������ ��� ��� - 1/0
		$dismiss = $offer->appendChild($document->createElement('dismiss'));
		$dismiss->appendChild($document->createTextNode(($row_agents['agent_is_active']=="1"? "0": "1")));
		
	}
		$result_ag_table->free();
		
		/*
			��������� ��������
		
		*/
		$flats_table_query="SELECT * FROM gcn_flats, gcn_flats_ext WHERE  gcn_flats_ext.adv_for_emls =1 AND gcn_flats.id = gcn_flats_ext.id AND objects_status = 0  AND removal_request = '1';";
		$result_flats_table = $db->query($flats_table_query);
		if (!$result_flats_table) {
        echo "���������� ��������� ������ ($ag_table_query) �� ��: " . mysql_error();
        exit;
    }
		if ($result_flats_table->num_rows == 0) {
        echo "������ � ������ ������� ������ 0 �����, ���������� ��������!";
        exit;
		}		
		
		//�������� � ������
		while($row_flats = $result_flats_table->fetch_assoc())
		{
			
			//���� ���� �� �������, �������� ��������� ���������� � �������
			
			if(!empty($row_flats['id_client'])){
				
				echo $row_flats['id_client'];
				
				$info_client = "SELECT * FROM `gcn_clients` WHERE `id`=".$row_flats['id_client'];
				$result_client = $db->query($info_client);
				
				//echo $info_client;
				
				if(!$result_client){
					echo "���������� ��������� ������ ($ag_table_query) �� ��: " . mysql_error();
					exit();
				}
				if($result_client){
					$res_client = $result_client->fetch_assoc();
					//var_dump($res_client);
				}
			}
			
			//���������� ��������� ��-�� offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//���������� ��������� id � ��������������� ������� �� ����� ��
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_flats['id'];
		$offer->appendChild($offerAttributeId);
		//���������� ��������� type �� ��������� "flat" � offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'flat';
		$offer->appendChild($offerAttributeType);

		//���������� �������� ��-��� � offer:
		//���� ���������� ������� � ����
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_flats['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//���� ����������
		if($row_flats['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_flats['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		/*
				��� <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//������
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_flats['id_reg']));
				//�����
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_flats['id_dept']));
				//���������� �����	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_flats['id_district']));
				//�����
					if($row_flats['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_flats['id_street']));}
				//����� ����
					if($row_flats['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_flats['house_number']));}
				//����� �������
					if($row_flats['house_korpus']){
					$house�ase = $location->appendChild($document->createElement('house-case'));
					$house�ase->appendChild($document->createTextNode($row_flats['house_korpus']));}
				//������	
					if($row_flats['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_flats['house_letter']));}
				//������� �����
					if($row_flats['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//�������
					//����� ���������
                        if($row_flats['id_metro']=='350025') {
                            $row_flats['id_metro'] = '22';
                        }
                     //����� �������   
                        if($row_flats['id_metro']=='350053') {
                            $row_flats['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_flats['id_metro']));
					}
				//��� ���������
					if($row_flats['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_flats['id_metro_transport']));}
				//��� <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id ������ �� ��
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_flats['id_user']));
				
				//���-�� ������ ����������� � ������
				$rooms = $offer->appendChild($document->createElement('rooms'));
				
				if($row_flats['id_flat_type'] == 40){
					$row_flats['amount_rooms'] = 1;
				}				
				$rooms->appendChild($document->createTextNode($row_flats['amount_rooms']));
				//������� ����
				$share_marker = $offer->appendChild($document->createElement('share-marker'));
				$share_marker->appendChild($document->createTextNode($row_flats['is_part']));
				//	���������� ������, �������� �� �����������
				$roomsType = $offer->appendChild($document->createElement('rooms-type'));
				$roomsType->appendChild($document->createTextNode($row_flats['id_flat_type']));
				//����� ������� ���� ��������  �.��.
				$allSpace = $offer->appendChild($document->createElement('all-space'));
				$allSpace->appendChild($document->createTextNode($row_flats['s_all']));
				//����� ������� �.��.
				$livingSpace = $offer->appendChild($document->createElement('living-space'));
				$livingSpace->appendChild($document->createTextNode($row_flats['s_life']));
				//������� ����� �.��.
				if($row_flats['id_flat_type'] != 40){
					$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
					$kitchenSpace->appendChild($document->createTextNode($row_flats['s_kitchen']));
				}				
				//	�������� �� �������� ������ 
				$living = $offer->appendChild($document->createElement('living'));
				$living->appendChild($document->createTextNode($row_flats['s_rooms']));
				//	������� �������� �.��.
				if($row_flats['s_corridor']){
				$corridorSpace = $offer->appendChild($document->createElement('corridor-space'));
				$corridorSpace->appendChild($document->createTextNode($row_flats['s_corridor']));}
				//������� �������� �.��.
				if($row_flats['s_vestibule']){
				$hallSpace = $offer->appendChild($document->createElement('hall-space'));
				$hallSpace->appendChild($document->createTextNode($row_flats['s_vestibule']));}
				//������ ������� � ������
				if($row_flats['s_ceiling']){
				$ceiling = $offer->appendChild($document->createElement('ceiling'));
				$ceiling->appendChild($document->createTextNode($row_flats['s_ceiling']));
				}
				//��� �������������, �������� �� �����������
				if($row_flats['id_type_property']){
				$propertyType = $offer->appendChild($document->createElement('property-type'));
				$propertyType->appendChild($document->createTextNode($row_flats['id_type_property']));
				}
				//	���-�� �������, ���� ����������� ��� ������. ???
				
				// 	���-�� �������, ���� ����������� ��� ������
				if($row_flats['tenants']){
				$tenants = $offer->appendChild($document->createElement('tenants'));
				$tenants->appendChild($document->createTextNode($row_flats['tenants']));
				}
				//	���-�� �����, ���� ����������� ��� ������
				if($row_flats['children']){
				$children = $offer->appendChild($document->createElement('children'));
				$children->appendChild($document->createTextNode($row_flats['children']));				
				}
				//	������� �������, ����������
				if($row_flats['id_balcony']){
				$balcony = $offer->appendChild($document->createElement('balcony'));
				$balcony->appendChild($document->createTextNode($row_flats['id_balcony']));	
				}
				// ��� �� ����, ����������
				if($row_flats['id_view_from_window']){
				$windowView = $offer->appendChild($document->createElement('window-view'));
				$windowView->appendChild($document->createTextNode($row_flats['id_view_from_window']));
				}
				// ��������� ��������, ����������
				if($row_flats['id_repair']){
				$quality = $offer->appendChild($document->createElement('quality'));
				$quality->appendChild($document->createTextNode(getQuality($row_flats['id_repair'])));
				}
				//	������� ��������, ����������
				if($row_flats['id_phone']){
				$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
				$phoneSupply->appendChild($document->createTextNode($row_flats['id_phone']));
				}
				//��� �������, ����������	
				if($row_flats['id_bathroom']){
				$bathroom = $offer->appendChild($document->createElement('bathroom'));
				$bathroom->appendChild($document->createTextNode($row_flats['id_bathroom']));
				}
				//��� �����, ����������
				if($row_flats['id_bath']){
				$bath = $offer->appendChild($document->createElement('bath'));
				$bath->appendChild($document->createTextNode($row_flats['id_bath']));
				}
				//	����������� ������� �����, ����������
				if($row_flats['id_hot_water']){
				$hotwaterSupply = $offer->appendChild($document->createElement('hotwater-supply'));
				$hotwaterSupply->appendChild($document->createTextNode($row_flats['id_hot_water']));
				}
				//�������� �������� ����, ����������
				if($row_flats['id_floor_material']){
				$floorCovering = $offer->appendChild($document->createElement('floor-covering'));
				$floorCovering->appendChild($document->createTextNode($row_flats['id_floor_material']));
				}
				//��������, 	����� �� 8000 ����.
				if($row_flats['comment_for_clients']){
				$description = $offer->appendChild($document->createElement('description'));
				$description->appendChild($document->createTextNode($row_flats['comment_for_clients']));
				}
				// ���� �������	
				$floor = $offer->appendChild($document->createElement('floor'));
				$floor->appendChild($document->createTextNode($row_flats['floor']));
				// 	��������� ������
				$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
				$floorsTotal->appendChild($document->createTextNode($row_flats['floor_all']));
				//��� ����, ����������
				$buildingType = $offer->appendChild($document->createElement('building-type'));
				$buildingType->appendChild($document->createTextNode($row_flats['id_type_house']));
				// ��� ���������
				if($row_flats['year_build']){
				$builtYear = $offer->appendChild($document->createElement('built-year'));
				$builtYear->appendChild($document->createTextNode($row_flats['year_build']));
				}
				// ��� ����������
				if($row_flats['year_capital_repair']){
				$repairYear = $offer->appendChild($document->createElement('repair-year'));
				$repairYear->appendChild($document->createTextNode($row_flats['year_capital_repair']));
				}
				
				//���� � ���, ����������
				if($row_flats['id_type_enter']){
				$buildingInput = $offer->appendChild($document->createElement('building-input'));
				$buildingInput->appendChild($document->createTextNode($row_flats['id_type_enter']));
				}
				//	������������, ����������
				if($row_flats['id_refuse_chute']){
				$rubbishChute = $offer->appendChild($document->createElement('rubbish-chute'));
				$rubbishChute->appendChild($document->createTextNode($row_flats['id_refuse_chute']));}
				// ������� �����, ����������
				if($row_flats['id_lift']){
				$lift = $offer->appendChild($document->createElement('lift'));
				$lift->appendChild($document->createTextNode($row_flats['id_lift']));}
				// ������ ������� �����, �
				if($row_flats['floor_first_height']){
				$groundfloorHeight = $offer->appendChild($document->createElement('groundfloor-height'));
				$groundfloorHeight->appendChild($document->createTextNode($row_flats['floor_first_height']));}
				//���� � ������
				$priceRub = $row_flats['price']*1000;
				$price = $offer->appendChild($document->createElement('price'));
				$price->appendChild($document->createTextNode($priceRub));
				//��� ������, ����������
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
				//���������� � ���� ��� ���������� (��� 25 ����.)
				if($row_flats['formula_rasselenia']){
				$priceNote = $offer->appendChild($document->createElement('price-note'));
				$priceNote->appendChild($document->createTextNode($row_flats['formula_rasselenia']));
				}
				//����������� ������� �� ������� (�1�/�0�)
				if($row_flats['hypothec']){
				$mortgage = $offer->appendChild($document->createElement('mortgage'));
				$mortgage->appendChild($document->createTextNode($row_flats['hypothec']));
				}
                //����������� (�1�/�0�)
                if($row_flats['apartment']) {
                    $apartment = $offer->appendChild($document->createElement('apartment'));
                    $apartment->appendChild($document->createTextNode($row_flats['apartment']));
            }
                //�������� (�1�/�0�)
                if($row_flats['penthouse']) {
                $penthouse = $offer->appendChild($document->createElement('penthouse'));
                $penthouse->appendChild($document->createTextNode($row_flats['penthouse']));
            }
				//��������
                if($row_flats['id_parking']) {
                $parking = $offer->appendChild($document->createElement('parking'));
                $parking->appendChild($document->createTextNode($row_flats['id_parking']));
            }
				//������
				if($row_flats['ramp']) {
                $ramp = $offer->appendChild($document->createElement('ramp'));
                $ramp->appendChild($document->createTextNode($row_flats['ramp']));
            }
				//��������� ����, ���� ������� ����
				if($row_flats['part_numerator']){
				$part_numerator = $offer->appendChild($document->createElement('part-numerator'));
				$part_numerator->appendChild($document->createTextNode($row_flats['part_numerator']));
				}
				//����������� ����, ���� ������� ����
				if($row_flats['part_denominator']){
				$part_denominator = $offer->appendChild($document->createElement('part-denominator'));
				$part_denominator->appendChild($document->createTextNode($row_flats['part-denominator']));
				}
				//�������
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_flats['auction_marker']));
				//��� �����
				if($row_flats['id_stove_plate']){
                $stove = $offer->appendChild($document->createElement('stove'));
                $stove->appendChild($document->createTextNode($row_flats['id_stove_plate']));
            }
				//������ �� �����
				if($row_flats['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_flats['video'])));
				}
				//����������
				$id_flat_image=$row_flats['id'];
				//������� ������ ����� ��������, id_base =1 --->��������
					$flats_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_flat_image AND id_base = 1 AND photo_status = 1";
					$result_flats_images= $db->query($flats_images_emls_query);
					//�������,���� ���� ���� ���� �����
					if($result_flats_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_flats_foto = $result_flats_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto'.$row_flats_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//����������
							if($row_flats_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_flats_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_flats_foto['photo_sorting']));}				
							//��������
							if($row_flats_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_flats_foto['photo_comment']));
							}					
					}
				}
				$result_flats_images->free();
	
				
				
		}	
						//��������� � ����������
				$result_flats_table->free();
				unset($row_flats);
				
				//����� ���������� �������
				
				
				$rooms_table_query="SELECT * FROM gcn_rooms, gcn_rooms_ext WHERE  gcn_rooms_ext.adv_for_emls =1 AND gcn_rooms.id = gcn_rooms_ext.id AND objects_status = 0 AND removal_request = '1';";
					$result_rooms_table = $db->query($rooms_table_query);
						if (!$result_rooms_table) {
							echo "���������� ��������� ������ ($rooms_table_query) �� ��: " . mysql_error();
							exit;
					}
					if ($result_rooms_table->num_rows == 0) {
						echo "������ � ������ ������� ������ 0 �����, ���������� ��������!";
							exit;
							}
				
				//�������� � ������
		while($row_flats = $result_rooms_table->fetch_assoc())
		{
			//���������� ��������� ��-�� offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//���������� ��������� id � ��������������� ������� �� ����� ��
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_flats['id'];
		$offer->appendChild($offerAttributeId);
		//���������� ��������� type �� ��������� "room" � offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'room';
		$offer->appendChild($offerAttributeType);
		
		//���������� �������� ��-��� � offer:
		//���� ���������� ������� � ����
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_flats['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//���� ����������
		if($row_flats['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_flats['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		
		/*
				��� <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//������
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_flats['id_reg']));
				//�����
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_flats['id_dept']));
				//���������� �����	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_flats['id_district']));
				//�����
					if($row_flats['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_flats['id_street']));}
				//����� ����
					if($row_flats['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_flats['house_number']));}
				//����� �������
					if($row_flats['house_korpus']){
					$house�ase = $location->appendChild($document->createElement('house-case'));
					$house�ase->appendChild($document->createTextNode($row_flats['house_korpus']));}
				//������	
					if($row_flats['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_flats['house_letter']));}
				//������� �����
					if($row_flats['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//�������
					//����� ���������
                        if($row_flats['id_metro']=='350025') {
                            $row_flats['id_metro'] = '22';
                        }
                     //����� �������   
                        if($row_flats['id_metro']=='350053') {
                            $row_flats['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_flats['id_metro']));
					}
				//��� ���������
					if($row_flats['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_flats['id_metro_transport']));}
		
		
		
			//��� <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id ������ �� ��
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_flats['id_user']));
		
				//���-�� ������ ����������� � ������
				$rooms = $offer->appendChild($document->createElement('rooms'));
				$rooms->appendChild($document->createTextNode($row_flats['amount_rooms']));
				//������� ����???
			
				//	���������� ������, �������� �� �����������
				$roomsType = $offer->appendChild($document->createElement('rooms-type'));
				$roomsType->appendChild($document->createTextNode($row_flats['id_flat_type']));
				//����� ������� ���� ��������  �.��.
				$allSpace = $offer->appendChild($document->createElement('all-space'));
				$allSpace->appendChild($document->createTextNode($row_flats['s_all']));
				//����������� ������� �.��.
				$livingSpace = $offer->appendChild($document->createElement('living-space'));
				$livingSpace->appendChild($document->createTextNode($row_flats['s_life']));
				//������� ����� �.��.
				$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
				$kitchenSpace->appendChild($document->createTextNode($row_flats['s_kitchen']));
				//	�������� �� �������� ������ 
				$living = $offer->appendChild($document->createElement('living'));
				$living->appendChild($document->createTextNode($row_flats['s_rooms']));
				//	������� �������� �.��.
				if($row_flats['s_corridor']){
				$corridorSpace = $offer->appendChild($document->createElement('corridor-space'));
				$corridorSpace->appendChild($document->createTextNode($row_flats['s_corridor']));}
				//������� �������� �.��.
				if($row_flats['s_vestibule']){
				$hallSpace = $offer->appendChild($document->createElement('hall-space'));
				$hallSpace->appendChild($document->createTextNode($row_flats['s_vestibule']));}
				//������ ������� � ������
				if($row_flats['s_ceiling']){
				$ceiling = $offer->appendChild($document->createElement('ceiling'));
				$ceiling->appendChild($document->createTextNode($row_flats['s_ceiling']));
				}
				//��� �������������, �������� �� �����������
				if($row_flats['id_type_property']){
				$propertyType = $offer->appendChild($document->createElement('property-type'));
				$propertyType->appendChild($document->createTextNode($row_flats['id_type_property']));
				}
				//	���-�� �������, ���� ����������� ��� ������. ???
				
				// 	���-�� �������, ���� ����������� ��� ������
				if($row_flats['tenants']){
				$tenants = $offer->appendChild($document->createElement('tenants'));
				$tenants->appendChild($document->createTextNode($row_flats['tenants']));
				}
				//	���-�� �����, ���� ����������� ��� ������
				if($row_flats['children']){
				$children = $offer->appendChild($document->createElement('children'));
				$children->appendChild($document->createTextNode($row_flats['children']));				
				}
				//	������� �������, ����������
				if($row_flats['id_balcony']){
				$balcony = $offer->appendChild($document->createElement('balcony'));
				$balcony->appendChild($document->createTextNode($row_flats['id_balcony']));	
				}
				// ��� �� ����, ����������
				if($row_flats['id_view_from_window']){
				$windowView = $offer->appendChild($document->createElement('window-view'));
				$windowView->appendChild($document->createTextNode($row_flats['id_view_from_window']));
				}
				// ��������� ��������, ����������
				if($row_flats['id_repair']){
				$quality = $offer->appendChild($document->createElement('quality'));
				$quality->appendChild($document->createTextNode(getQuality($row_flats['id_repair'])));
				}
				//	������� ��������, ����������
				if($row_flats['id_phone']){
				$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
				$phoneSupply->appendChild($document->createTextNode($row_flats['id_phone']));
				}
				//��� �������, ����������	
				if($row_flats['id_bathroom']){
				$bathroom = $offer->appendChild($document->createElement('bathroom'));
				$bathroom->appendChild($document->createTextNode($row_flats['id_bathroom']));
				}
				//��� �����, ����������
				if($row_flats['id_bath']){
				$bath = $offer->appendChild($document->createElement('bath'));
				$bath->appendChild($document->createTextNode($row_flats['id_bath']));
				}
				//	����������� ������� �����, ����������
				if($row_flats['id_hot_water']){
				$hotwaterSupply = $offer->appendChild($document->createElement('hotwater-supply'));
				$hotwaterSupply->appendChild($document->createTextNode($row_flats['id_hot_water']));
				}
				//�������� �������� ����, ����������
				if($row_flats['id_floor_material']){
				$floorCovering = $offer->appendChild($document->createElement('floor-covering'));
				$floorCovering->appendChild($document->createTextNode($row_flats['id_floor_material']));
				}
				//��������, 	����� �� 8000 ����.
				if($row_flats['comment_for_clients']){
				$description = $offer->appendChild($document->createElement('description'));
				$description->appendChild($document->createTextNode($row_flats['comment_for_clients']));
				}
				// ���� �������	
				$floor = $offer->appendChild($document->createElement('floor'));
				$floor->appendChild($document->createTextNode($row_flats['floor']));
				// 	��������� ������
				$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
				$floorsTotal->appendChild($document->createTextNode($row_flats['floor_all']));
				//��� ����, ����������
				$buildingType = $offer->appendChild($document->createElement('building-type'));
				$buildingType->appendChild($document->createTextNode($row_flats['id_type_house']));
				// ��� ���������
				if($row_flats['year_build']){
				$builtYear = $offer->appendChild($document->createElement('built-year'));
				$builtYear->appendChild($document->createTextNode($row_flats['year_build']));
				}
				// ��� ����������
				if($row_flats['year_capital_repair']){
				$repairYear = $offer->appendChild($document->createElement('repair-year'));
				$repairYear->appendChild($document->createTextNode($row_flats['year_capital_repair']));
				}
				
				//���� � ���, ����������
				if($row_flats['id_type_enter']){
				$buildingInput = $offer->appendChild($document->createElement('building-input'));
				$buildingInput->appendChild($document->createTextNode($row_flats['id_type_enter']));
				}
				//	������������, ����������
				if($row_flats['id_refuse_chute']){
				$rubbishChute = $offer->appendChild($document->createElement('rubbish-chute'));
				$rubbishChute->appendChild($document->createTextNode($row_flats['id_refuse_chute']));}
								//��������
                if($row_flats['id_parking']) {
                $parking = $offer->appendChild($document->createElement('parking'));
                $parking->appendChild($document->createTextNode($row_flats['id_parking']));
            }
				// ������� �����, ����������
				if($row_flats['id_lift']){
				$lift = $offer->appendChild($document->createElement('lift'));
				$lift->appendChild($document->createTextNode($row_flats['id_lift']));}
				// ������ ������� �����, �
				if($row_flats['floor_first_height']){
				$groundfloorHeight = $offer->appendChild($document->createElement('groundfloor-height'));
				$groundfloorHeight->appendChild($document->createTextNode($row_flats['floor_first_height']));}
				//���� � ������
				$priceRub = $row_flats['price']*1000;
				$price = $offer->appendChild($document->createElement('price'));
				$price->appendChild($document->createTextNode($priceRub));
				//��� ������, ����������
				if($row_flats['id_type_flat']){
				$dealType = $offer->appendChild($document->createElement('deal-type'));
				$dealType->appendChild($document->createTextNode($row_flats['id_type_flat']));}
				//���������� � ���� ��� ���������� (��� 25 ����.)
				if($row_flats['formula_rasselenia']){
				$priceNote = $offer->appendChild($document->createElement('price-note'));
				$priceNote->appendChild($document->createTextNode($row_flats['formula_rasselenia']));
				}
				//����������� ������� �� ������� (�1�/�0�)
				if($row_flats['hypothec']){
				$mortgage = $offer->appendChild($document->createElement('mortgage'));
				$mortgage->appendChild($document->createTextNode($row_flats['hypothec']));
				}
				//����� ���-�� ������, ������ � ������� ������.
				if($row_flats['amount_rooms_total']){
				$roomsTotal = $offer->appendChild($document->createElement('rooms-total'));
				$roomsTotal->appendChild($document->createTextNode($row_flats['amount_rooms_total']));
				}
				//�������
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_flats['auction_marker']));
				//������
				if($row_flats['ramp']) {
                $ramp = $offer->appendChild($document->createElement('ramp'));
                $ramp->appendChild($document->createTextNode($row_flats['ramp']));
				}
				//��� �����
				if($row_flats['id_stove_plate']) {
                $stove = $offer->appendChild($document->createElement('stove'));
                $stove->appendChild($document->createTextNode($row_flats['id_stove_plate']));
				}
				//������ �� �����
				if($row_flats['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_flats['video'])));
				}
						//����������
				$id_flat_image=$row_flats['id'];
				//������� ������ ����� ��������, id_base =2 --->�������
					$flats_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_flat_image AND id_base = 2 AND photo_status = 1";
					$result_flats_images= $db->query($flats_images_emls_query);
					//�������,���� ���� ���� ���� �����
					if($result_flats_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_flats_foto = $result_flats_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_flats_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//����������
							if($row_flats_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_flats_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_flats_foto['photo_sorting']));}
							//��������
							if($row_flats_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_flats_foto['photo_comment']));
							}					
					}
				}
				$result_flats_images->free();
		
		
		}
						//��������� � ���������
				$result_rooms_table->free();
				unset($row_flats);		
				//������ ���������
				
				$commer_table_query="SELECT * FROM gcn_comm, gcn_comm_ext WHERE  gcn_comm_ext.adv_for_emls =1 AND gcn_comm.id = gcn_comm_ext.id AND objects_status = 0  AND removal_request = '1' LIMIT 0,600;";
					$result_commer_table = $db->query($commer_table_query);
					if (!$result_commer_table) {
					echo "���������� ��������� ������ ($commer_table_query) �� ��: " . mysql_error();
					exit;
						}
					if ($result_commer_table->num_rows == 0) {
					echo "������ � ������ �������� ������ 0 �����, ���������� ��������!";
					exit;
						}
				
						//�������� � ������
		while($row_commer = $result_commer_table->fetch_assoc())
		{
			//���������� ��������� ��-�� offer
		$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
		//���������� ��������� id � ��������������� ������� �� ����� ��
		$offerAttributeId = $document->createAttribute('id');
		$offerAttributeId->value = $row_commer['id'];
		$offer->appendChild($offerAttributeId);
		//���������� ��������� type �� ��������� "commerce" � offer
		$offerAttributeType = $document->createAttribute('type');
		$offerAttributeType->value = 'commerce';
		$offer->appendChild($offerAttributeType);

		//���������� �������� ��-��� � offer:
		
		//���� ���������� ������� � ����
		$creationDate = $offer->appendChild($document->createElement('creation-date'));
		$cDate = date('Y-m-d', strtotime($row_commer['date_add']));
		$creationDate->appendChild($document->createTextNode($cDate));
		//���� ����������
		if($row_commer['date_edit']){
		$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
		$upDate = date('Y-m-d', strtotime($row_commer['date_update_emls']));
		$lastUpdateDate->appendChild($document->createTextNode($upDate));
		}
		/*
				��� <location>
		*/
				$location = $offer->appendChild($document->createElement('location'));
				//������
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_commer['id_reg']));
				//�����
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_commer['id_dept']));
				//���������� �����	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_commer['id_district']));
				//�����
				if($row_commer['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_commer['id_street']));}
				//����� ����
					if($row_commer['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_commer['house_number']));}
				//����� �������
					if($row_commer['house_korpus_fact']){
					$houseCase = $location->appendChild($document->createElement('house-case'));
					$houseCase->appendChild($document->createTextNode($row_commer['house_korpus_fact']));}
				//������	
					if($row_commer['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_commer['house_letter']));}
				//������� �����
					if($row_commer['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//�������
					//����� ���������
                        if($row_commer['id_metro']=='350025') {
                            $row_commer['id_metro'] = '22';
                        }
                     //����� �������   
                        if($row_commer['id_metro']=='350053') {
                            $row_commer['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_commer['id_metro']));
					}
				//��� ���������
					if($row_commer['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_commer['id_metro_transport']));}
		
				//��� <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id ������ �� ��
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_commer['id_user']));
					
				//	��� �������, ����������
					$category = $offer->appendChild($document->createElement('category'));
					
					$category->appendChild($document->createTextNode(get_emls_type_property($row_commer['id_type'])));
					
				//��� ������,����������� ��� ������/���������
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
				//������ ����������� ���������,����������� ��� ��������� 
				//1 -����� ���� 2-������� ����, � ����� ������ �������
				if($row_commer['id_type'] == 6 || $row_commer['id_type'] == 7){
					$subcategory = $offer->appendChild($document->createElement('subcategory'));
					$subcategory->appendChild($document->createTextNode('2'));
					
				}
				
				//��������� ����������� ������� ������ �� ������
				
				if(!empty($row_commer['space_min'])){
					$all_space_min = $offer->appendChild($document->createElement('all-space-min'));
					$all_space_min->appendChild($document->createTextNode($row_commer['space_min']));					
				}
				
				//	����������� ������
					$propertyType = $offer->appendChild($document->createElement('property-type'));
					$propertyType->appendChild($document->createTextNode($row_commer['id_property']));
				//����� �������, �.��.
					$allSpace = $offer->appendChild($document->createElement('all-space'));
					$allSpace->appendChild($document->createTextNode($row_commer['s_all']));
				//��������� ����������, ������������, ����������
					
					//�������� ���� ��� ����� ��������� ����� <value> 
					//����� ��������� � �������  explode ������ ���������� �� ��������� ���� <value>,"," �������� ������������,����� �������� ��������������� �������� emls �� ������� gcn_list_comm_use
				getIdEmlsUse();
				//���� � ������, ����������
				if($row_commer['id_entry']){
					$buildingInput = $offer->appendChild($document->createElement('building-input'));
					$buildingInput->appendChild($document->createTextNode($row_commer['id_entry']));}
					//$buildingInput->appendChild($document->createTextNode(getIdEmlsUse()."count"));}
		
					
				//����� ��������� ����� (��� 50 ����.)
				if($row_commer['floors_text']){
					$floor = $offer->appendChild($document->createElement('floor'));
					$floor->appendChild($document->createTextNode($row_commer['floors_text']));
				}
				//��������� ������
				if($row_commer['floors']){
					$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
					$floorsTotal->appendChild($document->createTextNode($row_commer['floors']));
				}
				//	��������� ���������, ����������.
				if($row_commer['id_cont']){
					$quality = $offer->appendChild($document->createElement('quality'));
					$quality->appendChild($document->createTextNode($row_commer['id_cont']));
				}
				//������������� , �1�/�0�
				if($row_commer['id_water']){
					$waterSupply = $offer->appendChild($document->createElement('water-supply'));
					$waterSupply->appendChild($document->createTextNode(($row_commer['id_water']=="2"?"1":"0")));
				}
				//����������� , �1�/�0�
				if($row_commer['id_sewer']){
					$sewerageSupply = $offer->appendChild($document->createElement('sewerage-supply'));
					$sewerageSupply->appendChild($document->createTextNode(($row_commer['id_sewer']=="2"?"1":"0")));
				}
				//��������������, �1�/�0�
					if($row_commer['id_heat']){
						$heatingSupply = $offer->appendChild($document->createElement('heating-supply'));
						$heatingSupply->appendChild($document->createTextNode(($row_commer['id_heat']=="2"?"1":"0")));
					}
				//����������������, �1�/�0�
					if($row_commer['id_elec']){
						$electricitySupply = $offer->appendChild($document->createElement('electricity-supply'));
						$electricitySupply->appendChild($document->createTextNode(($row_commer['id_elec']=="2"?"1":"0")));
					}
				//���������� �����, �1�/�0�
					if($row_commer['id_phone']){
						$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
						$phoneSupply->appendChild($document->createTextNode(($row_commer['id_phone']=="2"?"1":"0")));
					}
				//����, �1�/�0�
					if($row_commer['id_lift']){
						$lift = $offer->appendChild($document->createElement('lift'));
						$lift->appendChild($document->createTextNode(($row_commer['id_lift']=="2"?"1":"0")));
					}
				//�/� ����, �1�/�0�
					if($row_commer['id_rroad']){
						$railway = $offer->appendChild($document->createElement('railway'));
						$railway->appendChild($document->createTextNode(($row_commer['id_rroad']=="2"?"1":"0")));
					}
				//���������� ����, ����������	
					if($row_commer['id_access_road']){
						$accessRoad = $offer->appendChild($document->createElement('access-road'));
						$accessRoad->appendChild($document->createTextNode($row_commer['id_access_road']));
					}
				//������� ���������� �������, ��
					if($row_commer['area']){
					$lotArea = $offer->appendChild($document->createElement('lot-area'));
					$lotArea->appendChild($document->createTextNode($row_commer['area']/100));}
				//	���� ������ ����� � �����
					if($row_commer['area_for_years']){
					$lotRentYear = $offer->appendChild($document->createElement('lot-rent-year'));
					$lotRentYear->appendChild($document->createTextNode($row_commer['area_for_years']));
					}
				//	������������� �� ��������� ������� ,�1�/�0�
					$lotSale = $offer->appendChild($document->createElement('lot-sale'));
					$lotSale->appendChild($document->createTextNode(($row_commer['id_property_land']== "1"?"1":"0")));
				//	��������� ������, ����������,����������� ��� �����
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
				//	��������� �������� �������, �� 8000 ���.
					if($row_commer['comment_for_clients']){
					$description = $offer->appendChild($document->createElement('description'));
					$description->appendChild($document->createTextNode($row_commer['comment_for_clients']));
					}
				//���� �������, ���.	
					if($row_commer['price_s'] and $row_commer['id_price_s']=="7"){
					$priceSale= $offer->appendChild($document->createElement('price-sale'));
					$priceSale->appendChild($document->createTextNode($row_commer['price_s']));
					}
					else if($row_commer['price_s']){
					$priceSale= $offer->appendChild($document->createElement('price-sale'));
					$priceSale->appendChild($document->createTextNode($row_commer['price_s']*1000));}
				//	������� ���� �������
				/*
					���� �� ��������:
					6 - ������� ���� �� ��,
					7- ������� ���� �� ��.�.
					
				*/
					if($row_commer['price_s'] and $row_commer['id_price_s']){
					$priceSaleUnit= $offer->appendChild($document->createElement('price-sale-unit'));
					$priceSaleUnit->appendChild($document->createTextNode(($row_commer['id_price_s']=="7"?"7":"6")));
					}
				//���� ������, ���.
				//���� ����� id_price_ar = 8(��� ���) �� ��������� �� ����� ������ �����
					if($row_commer['price_ar'] and $row_commer['id_price_ar']=="8"){
					$priceRent= $offer->appendChild($document->createElement('price-rent'));
					$priceRent->appendChild($document->createTextNode($row_commer['price_ar']*1000));
					}
					else if($row_commer['price_ar']){
					$priceRent= $offer->appendChild($document->createElement('price-rent'));
					$priceRent->appendChild($document->createTextNode($row_commer['price_ar']));
					}
				//	������� ���� ������
				/*
					���� �� ��������:
					2 - ���./� � ���.
					3 - ���./� � ���.
					4 - ���. � ���. �� ��
					� �� id �� �� ����������,������� ���� ���� ����� "8"(���.���. � �����) �� ������ 4,���� ��� ��������� -�� ������ 2(���./���� � �����)
				*/
					if($row_commer['price_ar'] and $row_commer['id_price_ar']){
					$priceRent= $offer->appendChild($document->createElement('price-rent-unit'));
					$priceRent->appendChild($document->createTextNode(($row_commer['id_price_ar']=="8"?"4":"2")));
					}
				
				//���� ���
					if($row_commer['price_ar_s']){
					$priceSaleRent= $offer->appendChild($document->createElement('price-salerent'));
					$priceSaleRent->appendChild($document->createTextNode($row_commer['price_ar_s']*1000));
					}
				//������� ���� ���
				/*
					���� �� ��������:
					8 - ������� ���� �� ��,
					9 - ������� ���� �� ��.�.
				*/
					if($row_commer['price_ar_s'] and $row_commer['id_price_ar_s']){
					$priceSaleRentUnit= $offer->appendChild($document->createElement('price-salerent-unit'));
					$priceSaleRentUnit->appendChild($document->createTextNode(($row_commer['id_price_ar_s']=="8"?"8":"9")));
					}
					
				//���������� � ����
					if($row_commer['price_comment']){
					$priceNote = $offer->appendChild($document->createElement('price-note'));
					$priceNote->appendChild($document->createTextNode($row_commer['price_comment']));
					}
				//������ �������� ������, %
					$agentFee = $offer->appendChild($document->createElement('agent-fee'));
					$agentFee->appendChild($document->createTextNode($row_commer['agent_fee']));
					
				//���������� (���) (������ ��� ������),	����������� ��� ������
				if($row_commer['price_ar']){
					$rentAdvancePayment = $offer->appendChild($document->createElement('rent-advance-payment'));
					$rentAdvancePayment->appendChild($document->createTextNode('1'));
				}
					
				//����������� ������� �� �������
					if($row_commer['hypothec']){
					$mortgage = $offer->appendChild($document->createElement('mortgage'));
					$mortgage->appendChild($document->createTextNode($row_commer['hypothec']));					
					}
				//�����
				if($row_commer['garage']){
					$garage = $offer->appendChild($document->createElement('garage'));
					$garage->appendChild($document->createTextNode($row_commer['garage']));
				}
				//��� ������
				if($row_commer['garage_type']){
					$garage_type = $offer->appendChild($document->createElement('garage_type'));
					$garage_type->appendChild($document->createTextNode($row_commer['garage_type']));
				}
				//��� ��������� ������
				if($row_commer['garage_box_type']){
					$garage_box_type = $offer->appendChild($document->createElement('garage_box_type'));
					$garage_box_type->appendChild($document->createTextNode($row_commer['garage_box_type']));
				}
				//������ ������������� ������
				if($row_commer['garage_status']){
					$garage_status = $offer->appendChild($document->createElement('garage_status'));
					$garage_status->appendChild($document->createTextNode($row_commer['garage_status']));
				}
				//�������
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_commer['auction_marker']));
				/*
				���������������
				<commercialvat>
				<id>2</id>
				<name>��� �������</name>
				</commercialvat>
				<commercialvat>
				<id>3</id>
				<name>��� �� �������</name>
				</commercialvat>
				<commercialvat>
				<id>4</id>
				<name>��� (���������� ������� ���������������)</name>
				</commercialvat>

				*/
				$vat = $offer->appendChild($document->createElement('vat'));
				$vat->appendChild($document->createTextNode($row_commer['id_nalog']));

										//����������
				$id_commer_image=$row_commer['id'];
				//������� ������ ����� ��������, id_base =5 --->���������
					$commer_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_commer_image AND id_base = 5 AND photo_status = 1";
					$result_commer_images= $db->query($commer_images_emls_query);
					//�������,���� ���� ���� ���� �����
					if($result_commer_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_commer_foto = $result_commer_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_commer_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//����������
							if($row_commer_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_commer_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_commer_foto['photo_sorting']));
							}
												
							//��������
							if($row_commer_foto['photo_comment']){
							$comment=$image->appendchild($document->createelement('comment'));
							$comment->appendchild($document->createTextNode($row_commer_foto['photo_comment']));
							}					
					}
				}
				$result_commer_images->free();

	}				
					
					
						
							
							
							//��������� � ����������
					$result_commer_table->free();
					unset($row_commer);	
				
								//������ ���������� ������������
				
				$farm_table_query="SELECT * FROM gcn_farm, gcn_farm_ext WHERE  gcn_farm_ext.adv_for_emls =1 AND gcn_farm.id = gcn_farm_ext.id AND objects_status = 0  AND removal_request = '1';";
					$result_farm_table = $db->query($farm_table_query);
					if (!$result_farm_table) {
					echo "���������� ��������� ������ ($farm_table_query) �� ��: " . mysql_error();
					exit;
						}
					if ($result_farm_table->num_rows == 0) {
					echo "������ � ������ �������� ������ 0 �����, ���������� ��������!";
					exit;
						}
						//�������� � ������
						while($row_farm = $result_farm_table->fetch_assoc())
		{
							//���������� ��������� ��-�� offer
				$offer = $emls_realty_feed ->appendChild($document->createElement('offer'));
					//���������� ��������� id � ��������������� ������� �� ����� ��
					$offerAttributeId = $document->createAttribute('id');
					$offerAttributeId->value = $row_farm['id'];
					$offer->appendChild($offerAttributeId);
					//���������� ��������� type �� ��������� "country" � offer
					$offerAttributeType = $document->createAttribute('type');
					$offerAttributeType->value = 'country';
					$offer->appendChild($offerAttributeType);

					//���������� �������� ��-��� � offer:
					
						
					//���� ���������� ������� � ����
				$creationDate = $offer->appendChild($document->createElement('creation-date'));
					$cDate = date('Y-m-d', strtotime($row_farm['date_add']));
					$creationDate->appendChild($document->createTextNode($cDate));
					//���� ����������
			if($row_farm['date_edit']){
				$lastUpdateDate = $offer->appendChild($document->createElement('last-update-date'));
					$upDate = date('Y-m-d', strtotime($row_farm['date_update_emls']));
					$lastUpdateDate->appendChild($document->createTextNode($upDate));
				}	
					
				/*
				��� <location>
					*/
				$location = $offer->appendChild($document->createElement('location'));
				//������
					$region = $location->appendChild($document->createElement('region'));
					$region->appendChild($document->createTextNode($row_farm['id_reg']));
				//�����
					$district = $location->appendChild($document->createElement('district'));
					$district->appendChild($document->createTextNode($row_farm['id_dept']));
				//���������� �����	
					$locality = $location->appendChild($document->createElement('locality'));
					$locality->appendChild($document->createTextNode($row_farm['id_district']));
				//�����
				if($row_farm['id_street']){
					$street = $location->appendChild($document->createElement('street'));
					$street->appendChild($document->createTextNode($row_farm['id_street']));}
				//����� ����
					if($row_farm['house_number']){
					$houseNumber = $location->appendChild($document->createElement('house-number'));
					$houseNumber->appendChild($document->createTextNode($row_farm['house_number']));}
				//����� �������
					if($row_farm['house_korpus']){
					$house�ase = $location->appendChild($document->createElement('house-case'));
					$house�ase->appendChild($document->createTextNode($row_farm['house_korpus']));}
				//������	
					if($row_farm['house_letter']){
					$houseLetter = $location->appendChild($document->createElement('house-letter'));
					$houseLetter->appendChild($document->createTextNode($row_farm['house_letter']));}
				//������� �����
					if($row_farm['id_metro']){
					$station = $location->appendChild($document->createElement('station'));
					//�������
					//����� ���������
                        if($row_farm['id_metro']=='350025') {
                            $row_farm['id_metro'] = '22';
                        }
                     //����� �������   
                        if($row_farm['id_metro']=='350053') {
                            $row_farm['id_metro'] = '97';
                        }
					$station->appendChild($document->createTextNode($row_farm['id_metro']));
					}
				//��� ���������
					if($row_farm['id_metro_transport']){
					$stationHowget = $location->appendChild($document->createElement('station-howget'));
					$stationHowget->appendChild($document->createTextNode($row_farm['id_metro_transport']));}
					
					
					//��� <sales-agent>
				$salesAgent = $offer->appendChild($document->createElement('sales-agent'));
					//id ������ �� ��
					$agentId = $salesAgent->appendChild($document->createElement('agent-id'));
					$agentId->appendChild($document->createTextNode($row_farm['id_user']));
					
									//	��� �������, ����������
					$category = $offer->appendChild($document->createElement('category'));
					$category->appendChild($document->createTextNode($row_farm['id_obj_type']));
									//��� �������������, ����������
					$propertyType = $offer->appendChild($document->createElement('property-type'));
					$propertyType->appendChild($document->createTextNode($row_farm['id_type_property']));
						//���������� ����, ����������	
					if($row_farm['id_ready']){
					$quality = $offer->appendChild($document->createElement('quality'));
					$quality->appendChild($document->createTextNode($row_farm['id_ready']));
				}
					//������� ������������, ����������	
					if($row_farm['id_proper']){
					$privatization = $offer->appendChild($document->createElement('privatization'));
					$privatization->appendChild($document->createTextNode($row_farm['id_proper']));}
					//���������� ����������, ����������
					if($row_farm['id_ready_doc']){
					$documentReady = $offer->appendChild($document->createElement('document-ready'));
					$documentReady->appendChild($document->createTextNode($row_farm['id_ready_doc']));}
					//����������� �� �.�. ������� (��)
					if($row_farm['km_from_station']){
					$distance = $offer->appendChild($document->createElement('distance'));
					$distance->appendChild($document->createTextNode($row_farm['km_from_station']));}
					//������������ ���������, ����������
					if($row_farm['id_pod']){
					$transport = $offer->appendChild($document->createElement('transport'));
					$transport->appendChild($document->createTextNode($row_farm['id_pod']));}
					//���������� ������, ����������
					if($row_farm['id_road']){
					$road = $offer->appendChild($document->createElement('road'));
					$road->appendChild($document->createTextNode($row_farm['id_road']));}
					//������� �������, ����������
					if($row_farm['id_pond']){
					$pond = $offer->appendChild($document->createElement('pond'));
					$pond->appendChild($document->createTextNode($row_farm['id_pond']));}
					//�������� ���������, ����������
					if($row_farm['id_head']){
					$heatingSupply = $offer->appendChild($document->createElement('heating-supply'));
					$heatingSupply->appendChild($document->createTextNode($row_farm['id_head']));}
					//�����������, ����������
					if($row_farm['id_wc']){
					$toilet = $offer->appendChild($document->createElement('toilet'));
					$toilet->appendChild($document->createTextNode($row_farm['id_wc']));}
					//������� ��������, ����������
					if($row_farm['id_phones']){
					$phoneSupply = $offer->appendChild($document->createElement('phone-supply'));
					$phoneSupply->appendChild($document->createTextNode($row_farm['id_phones']));}
					//�������������, ����������
					if($row_farm['id_electro']){
					$electricitySupply = $offer->appendChild($document->createElement('electricity-supply'));
					$electricitySupply->appendChild($document->createTextNode($row_farm['id_electro']));}
					//�������������, ����������
					if($row_farm['id_water']){
					$waterSupply = $offer->appendChild($document->createElement('water-supply'));
					$waterSupply->appendChild($document->createTextNode($row_farm['id_water']));}
					//�������������, ����������
					if($row_farm['id_gas']){
					$gasSupply = $offer->appendChild($document->createElement('gas-supply'));
					$gasSupply->appendChild($document->createTextNode($row_farm['id_gas']));}
					//������� �������, ���.
					$lotArea = $offer->appendChild($document->createElement('lot-area'));
					$lotArea->appendChild($document->createTextNode($row_farm['area']));
					//��������� ���������� �������, ����������
					if($row_farm['id_area_cond']){
					$lotCondition = $offer->appendChild($document->createElement('lot-condition'));
					$lotCondition->appendChild($document->createTextNode($row_farm['id_area_cond']));}
					//����������� ������ �����, ����������
					if($row_farm['id_area_property']){
					$lotProperty = $offer->appendChild($document->createElement('lot-property'));
					$lotProperty->appendChild($document->createTextNode($row_farm['id_area_property']));}
					//������ �����
					if($row_farm['floors']){
					$floorsTotal = $offer->appendChild($document->createElement('floors-total'));
					$floorsTotal->appendChild($document->createTextNode($row_farm['floors']));}
					//�������� ����, �.6�8
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
					
					//���������� ������
					if($row_farm['amount_rooms']){
					$rooms = $offer->appendChild($document->createElement('rooms'));
					$rooms->appendChild($document->createTextNode($row_farm['amount_rooms']));}
					//����� �������, �.��. , ������ ��� ����.
					if($row_farm['s_total']){
					$allSpace = $offer->appendChild($document->createElement('all-space'));
					$allSpace->appendChild($document->createTextNode($row_farm['s_total']));}
					//����� �������, �.��.
					if($row_farm['s_rooms']){
					$livingSpace = $offer->appendChild($document->createElement('living-space'));
					$livingSpace->appendChild($document->createTextNode($row_farm['s_rooms']));}
					//������� �����, �.��.
					if($row_farm['s_kitchen']){
					$kitchenSpace = $offer->appendChild($document->createElement('kitchen-space'));
					$kitchenSpace->appendChild($document->createTextNode($row_farm['s_kitchen']));}
					//����� ������� �� ��������
					if($row_farm['str_s_rooms']){
					$living = $offer->appendChild($document->createElement('living'));
					$living->appendChild($document->createTextNode($row_farm['str_s_rooms']));}
					//��� ��������� ����
					if($row_farm['year_build']){
					$builtYear = $offer->appendChild($document->createElement('built-year'));
					$builtYear->appendChild($document->createTextNode($row_farm['year_build']));}
					//�������� ���� ����, ����������
					if($row_farm['id_wall_mater']){
					$buildingType = $offer->appendChild($document->createElement('building-type'));
					$buildingType->appendChild($document->createTextNode($row_farm['id_wall_mater']));}
					//��������� ����, ����������
					if($row_farm['id_funda']){
					$buildingBase = $offer->appendChild($document->createElement('building-base'));
					$buildingBase->appendChild($document->createTextNode($row_farm['id_funda']));}
					//����� ����, ����������
					if($row_farm['id_roof']){
					$buildingRoof = $offer->appendChild($document->createElement('building-roof'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_roof']));}
					
					//���������
					if($row_farm['id_ready_doc']){
					$buildingRoof = $offer->appendChild($document->createElement('document-ready'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_ready_doc']));}
					//������
					if($row_farm['id_road']){
					$buildingRoof = $offer->appendChild($document->createElement('road'));
					$buildingRoof->appendChild($document->createTextNode($row_farm['id_road']));}
					//	�����.�������, ����������
					if($row_farm['id_interior']){
					$interior = $offer->appendChild($document->createElement('interior'));
					$interior->appendChild($document->createTextNode($row_farm['id_interior']));}
					//�������������� ���������, ������������, ����������
					//�������� ���� ��� ����� ��������� ����� <value> �� ���������� ��
					//http://emls.ru/api/v1.0/list/countrypostr ,��������� ����������� ��� � ���������
					if($row_farm['str_extra']){
					$additional�onstructions = $offer->appendChild($document->createElement('additional-constructions'));
						$dop_value=explode(",",$row_farm['str_extra']);
						$i=0;
						while ($i<count($dop_value)){
							$emls_value = $dop_value[$i];
							//������ ��������� �� ��� integer,����� ����
							settype($emls_value,"int");
							$value = $additional�onstructions->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					//���������� ��������, ������������, ����������
					if($row_farm['str_near']){
					$landscape = $offer->appendChild($document->createElement('landscape'));
						$near_value=explode(",",$row_farm['str_near']);
						$i=0;
						while ($i<count($near_value)){
							$emls_value = $near_value[$i];
							//������ ��������� �� ��� integer,����� ����
							settype($emls_value,"int");
							$value = $landscape->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					
				//��������������, ������������, ����������	
					if($row_farm['str_infra']){
						$infrastructure = $offer->appendChild($document->createElement('infrastructure'));
						$infra_value=explode(",",$row_farm['str_infra']);
						$i=0;
						while ($i<count($infra_value)){
							$emls_value = $infra_value[$i];
							//������ ��������� �� ��� integer,����� ����
							settype($emls_value,"int");
							$value = $infrastructure->appendChild($document->createElement('value'));
							$value->appendChild($document->createTextNode($emls_value));
							$i++;
						}
					}
					
				//�������������� �������� ����������� ���� (�� 8000 ���.).
					if($row_farm['comment_for_clients']){
					$description = $offer->appendChild($document->createElement('description'));
					$description->appendChild($document->createTextNode($row_farm['comment_for_clients']));}
				
				//����, ���.
					$price = $offer->appendChild($document->createElement('price'));
					$price->appendChild($document->createTextNode($row_farm['price']*1000));
					
				//���� �� ��������
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
					
				//	���������� � ���� (��� 25 ����.)
					if($row_farm['price_comment']){
					$priceNote = $offer->appendChild($document->createElement('price-note'));
					$priceNote->appendChild($document->createTextNode($row_farm['price_comment']));}
				//����������� ������� �� �������
					if($row_farm['hypothec']){
					$mortgage = $offer->appendChild($document->createElement('mortgage'));
					$mortgage->appendChild($document->createTextNode($row_farm['hypothec']));}
					
				//�������
				$auction_marker = $offer->appendChild($document->createElement('auction-marker'));
				$auction_marker->appendChild($document->createTextNode($row_farm['auction_marker']));	
				
					//������ �� �����
				if($row_farm['video']){
				$videos = $offer->appendChild($document->createElement('videos'));
				$video = $videos->appendChild($document->createElement('video'));
				$url = $video->appendChild($document->createElement('url'));
				$url->appendChild($document->createTextNode(htmlspecialchars($row_farm['video'])));
				}
															//����������
				$id_farm_image=$row_farm['id'];
				//������� ������ ����� ��������, id_base =3 --->�������
					$farm_images_emls_query= "SELECT * FROM gcn_foto WHERE id_object = $id_farm_image AND id_base = 3 AND photo_status = 1";
					$result_farm_images= $db->query($farm_images_emls_query);
					//�������,���� ���� ���� ���� �����
					if($result_farm_images->num_rows>0){
					$images=$offer->appendchild($document->createelement('images'));
						while ($row_farm_foto = $result_farm_images->fetch_assoc()){
							$str_foto = 'http://agent.gcn-spb.ru/agent/foto/'.$row_farm_foto['photo_file'];
							$image=$images->appendchild($document->createelement('image'));
							$url=$image->appendchild($document->createelement('url'));
							$url->appendchild($document->createTextNode($str_foto));
							//����������
							if($row_farm_foto['photo_sorting']=="0"){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode("0"));}
							else if($row_farm_foto['photo_sorting']){
							$sort=$image->appendchild($document->createelement('sort'));
							$sort->appendchild($document->createTextNode($row_farm_foto['photo_sorting']));
							}
												
							//��������
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
		
		
//��������� xml
$document->formatOutput = true; // ��������� �������� formatOutput
                           // domDocument � �������� true
						   

$test1 = $document->saveXML(); // �������� ������ � test1
//��������� ������������� ���
//���� � ����� �������  ����� ���������� � ������
$currentTime=date('Y-m-d-H-i-s');
$fp = fopen('emls.log',"a+");
$log=fwrite($fp,$currentTime);
//�������� ������ � ����
$log=fwrite($fp,$test1);
$closeLog=fclose($fp);
if(!$log && !$closeLog){
	echo "������ ��� ������ ���� �������� �����";
}


$file = $document->save('/var/www/html/gcn/emls_xml/emls.xml'); // ���������� �����
if($file)
	echo "complete. \r\n ";
else echo "some error";
?>