.<?php
//Делаем xml файл для выгрузки в Яндекс.Недвижимость
error_reporting(E_ALL);
//Сначала будем передавать квартиры,запишем все необходимые переменные
$company="Городской Центр Недвижимости";
$type='продажа';
$property_type='жилая';
$category='квартира';
$name='Городской Центр Недвижимости';
//$phone='(812)318-08-68';
$url='www.gcn-spb.ru';
$country='Роccия';
$type_seller='агентство';
$b=1000;//Переменная нужна,чтобы конвертировать цену (тыс.р. в RUR),умножаем на нее в дальнейшем $row['price']
$unit='кв.м';
$currency='RUR';
$objDateTime = new DateTime('NOW');//В эту переменную я записал текущее время и дату для последующего	 форматирования по стандарту  ISO 8601,необходимого для передачи в yml в тег <creation-date>,<generation-date>
require_once('/databasegcn/includes/config.php');
require_once('/databasegcn/includes/functions.php');
$file="/var/www/html/gcn/realty-yandex/domclick.xml";
//$file="/var/www/html/gcn/temp/ya.xml";
//Создаем фуекцию для корректной передачи фоток в теги <image>.Цикл нужен для того,чтобы передать максимальное кол-во фоток в разных тегах,но не больше 11.Если фотографий нет,работа функции прерывается и тэг <image> не ставится.
function get_foto_yandex_flats($id_item,$id_base){
	global $db;
	$res=$db ->query("SELECT *  FROM `gcn_foto` WHERE `id_object`=".$id_item." AND `id_base`=".$id_base." AND `photo_status`='1'");
	$x=0;
	$r = '';
	while($row=$res->fetch_assoc()){

		$r.='<image> http://agent.gcn-spb.ru/agent/foto'.$row['photo_file'].'</image>'."\r\n";
		$x++;
		if($x>30)
			return $r;
	}
	$x=0;
	return $r;
}	
	//И еще функцию для передачи фоток по комнатам
function get_foto_yandex_room($id_item,$id_base){
	global $db;
	$res=$db ->query("SELECT *  FROM `gcn_foto` WHERE `id_object`=".$id_item." AND `id_base`=".$id_base." AND `photo_status`='1'");
	$x=0;
	$r = '';
	while($row=$res->fetch_assoc()){
		$r.='<image> http://agent.gcn-spb.ru/agent/foto'.'/'.$row['photo_file'].'</image>'."\r\n";
		$x++;
		if($x>30)
	
		return $r;
	}
	$x=0;
	return $r;
}

/*function constuctSQLAll($tbl = 'gcn_foto', $where = array()) 
{
	
	$where = array( 'id_object' => array('=',$id_item), 
					'id_base' => array('=',$id_base), 
					'photo_status' => array('=',1) );
	
//	SELECT *  FROM `gcn_foto` WHERE `id_object`=".$id_item." AND `id_base`=".$id_base." AND `photo_status`='1'	
	$str = '';	
	$oper = ' AND ';
	foreach($where as $el => $constract) {
		$str .= "`".$el."`=" . $constract[0].$constract[1].$oper;		
	}
	$str = substr($str, 0, count($oper));
	
	echo $str; die();
	return 'SELECT *  FROM `'.$tbl.'` WHERE '.$str;
	
}*/

//по загородной недвижимости

function get_foto_yandex_farm($id_item,$id_base){
	global $db;
$res=$db ->query("SELECT *  FROM `gcn_foto` WHERE `id_object`=".$id_item." AND `id_base`=".$id_base." AND `photo_status`='1'");
	$x=0;
	$r = '';
	while($row=$res->fetch_assoc()){
		$r.='<image> http://agent.gcn-spb.ru/agent/foto'.'/'.$row['photo_file'].'</image>'."\r\n";
		$x++;
		if($x>30)
			return $r;
	}
	$x=0;
	return $r;
}
//коммерция
function get_foto_yandex_comm($id_item,$id_base){
	global $db;
$res=$db ->query("SELECT *  FROM `gcn_foto` WHERE `id_object`=".$id_item." AND `id_base`=".$id_base." AND `photo_status`='1'");
	$x=0;
	$r = '';
	while($row=$res->fetch_assoc()){
		$r.='<image> http://agent.gcn-spb.ru/agent/foto'.'/'.$row['photo_file'].'</image>'."\r\n";
		$x++;
		if($x>30)
			return $r;
	}
	$x=0;
	return $r;
}
//создаем функцию,исправляющую значения в БД Есть/Нет на 1/0
function true_false_yandex($tag,$param2){
	$re = "";
if ($param2=="Есть")
{$param2=true;
	$re='<'.$tag.'>'.$param2.'</'.$tag.'>'."\r\n";
}
else if($param2=="Нет")
	$re='<'.$tag.'>'.'0'.'</'.$tag.'>'."\r\n";
return $re;
}
//функция вытаскивает корректное значение из таблицы gcn_list_balcony
function balcony_fix($id_item){
	global $db;

$res=$db ->query("SELECT * FROM `gcn_list_balcony` WHERE `id_item`=".$id_item."");
$r = '';
if($row=$res->fetch_assoc())

 $r='<balcony>'.$row['item_name'].'</balcony>'."\r\n";
return $r;
}
//функция вытаскивает корректное значение из таблицы gcn_list_country_wc
function toilet_fix($id_item){
	global $db;
	$r = '';

$res=$db ->query("SELECT * FROM `gcn_list_country_wc` WHERE `id_item`=".$id_item."");
if($row=$res->fetch_assoc())
	if(!empty($row['yandex_item']))
	$r='<toilet>'.$row['yandex_item'].'</toilet>'."\r\n";
return $r;
}


//функция вытаскивает корректное значение из таблицы gcn_list_type_object_country и исправляет значения для формата яндекса
function obj_type_fix_farm($id_item){

	global $db;
$res=$db ->query("SELECT * FROM `gcn_list_type_object_country` WHERE `id_item`=".$id_item."");
if($row=$res->fetch_assoc())
	$item_name=$row['item_name'];
    $item_name=str_replace("Дача","дача",$item_name);
	$item_name=str_replace("Зимний дом","дом",$item_name);
	$item_name=str_replace("Участок","земельный участок",$item_name);
	$item_name=str_replace("Коттедж","cottage",$item_name);
	$item_name=str_replace("Часть дома","часть дома",$item_name);
	$item_name=str_replace("Таунхауз","таунхаус",$item_name);
	$row['item_name']=$item_name;
	$r='<category>'.$row['item_name'].'</category>'."\r\n";
	return $r;
}
//Создаем функцию для заполнения тега (</creation-date>),в качестве аргумента используем элемент массива date_edit(в БД дата последнего редактирования)
	function get_Datetime_ISO8601($date_edit) {
    $tz_object = new DateTimeZone('Europe/Moscow');
    //date_default_timezone_set('Europe/Moscow');

    $datetime = new DateTime($date_edit);
    $datetime->setTimezone($tz_object);
    return $datetime->format('c');
}
echo "Стартуем \r\n";
//Делаем необходимый запрос активных квартир к mysql,помещаем извлеченные данные в ассоциативный массив,c помощью цикла начинаем запись.

$res=$db ->query("SELECT *  FROM `gcn_flats`,`gcn_flats_ext`,`gcn_kadr` WHERE  gcn_flats.id_user=gcn_kadr.id_agent  and gcn_flats.id = gcn_flats_ext.id and gcn_flats.objects_status ='0' and gcn_flats_ext.adv_for_dk = 1 AND gcn_flats.removal_request = '1'");
if($res)
echo "Запрос к mysql завершен \r\n";
else{
	echo "Ошибка запроса к базе данных \r\n";
	exit();
}
echo "начинаем запись в файл...\r\n";
$r = "";
$r='<?xml version="1.0" encoding="utf-8"?>'."\r\n".'<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">'."\r\n";
$r.='<generation-date>'.$objDateTime->format('c').'</generation-date>'."\r\n";
while($row=$res ->fetch_assoc())
{
	
	$r.='<offer internal-id'.'="'.$row['id'].'">'."\r\n";
	$r.='<type>'.$type.'</type>'."\r\n";
	$r.='<property-type>'.$property_type.'</property-type>'."\r\n";
	$r.='<category>'.$category.'</category>'."\r\n";
		if($row['id_flat_type'] == '40'){
		$r.='<studio>1</studio>'."\r\n";
	}
	$r.='<url>'.'http://www.gcn-spb.ru/kvartiry/details.php?t=flat&amp;id='.$row['id'].'</url>'."\r\n";
	if(!empty($row['cadastral_number']))
	$r.='<cadastral-number>'.htmlspecialchars($row['cadastral_number']).'</cadastral-number>'."\r\n";
	$r.='<creation-date>'.get_Datetime_ISO8601($row['date_edit']).'</creation-date>'."\r\n";	
	if($row['date_edit'])
    $r.='<last-update-date>'.get_Datetime_ISO8601($row['date_update_emls']).'</last-update-date>'."\r\n";
	$r.='<location>'."\r\n";
	$r.='<country>'.$country.'</country>'."\r\n";
	$r.='<region>'.$row['str_reg'].'</region>'."\r\n";
	if($row['id_reg'] != '2'){
		$district=str_replace("пгт.","поселок городского типа",$row['str_district']);
		$r.='<district>'.$district.'</district>'."\r\n";
	}	
		$locality_name=str_replace("пгт.","поселок городского типа",$row['str_dept']);
		$r.='<locality-name>'.$locality_name.'</locality-name>'."\r\n";
		if($row['id_reg'] == '2'){
		$sub_locality_name=$row['str_district'];
		$sub_locality_name=str_replace("пгт.","поселок городского типа",$sub_locality_name);
	    $r.='<sub-locality-name>'.$sub_locality_name.'</sub-locality-name>'."\r\n";
	}

	$street=$row['str_street'];
	$street=str_replace("бул.","бульвар", $street);
	$street=str_replace("Кр.Село","Красное Село", $street);
	$street=str_replace("шос.","шоссе", $street);
	$street=str_replace("кан. наб.","канала набережная",$street);
	$row['str_street']=$street;
	$corpus=$row['house_korpus'];
	if($corpus){
		$r.='<address>'.$row['str_street'].','.$row['house_number'].',к.'.$corpus.'</address>'."\r\n";
	}
	else {
		$r.='<address>'.$row['str_street'].','.$row['house_number'].'</address>'."\r\n";
	}
	$r.='<metro>'."\r\n";
	$r.='<name>'.$row['str_metro'].'</name>'."\r\n";
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'пеш')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);	
	$r.='<time-on-foot>'.$matches[0][0].'</time-on-foot>'."\r\n";
	}
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'тр')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);
	$r.='<time-on-transport>'.$matches[0][0].'</time-on-transport>'."\r\n";
	}
	$r.='</metro>'."\r\n";
	$r.='</location>'."\r\n";
	$r.='<sales-agent>'."\r\n";
	$r.='<name>'.$row['str_user'].'</name>'."\r\n";
	$r.='<phone>'.$row['phone'].'</phone>'."\r\n";
	//$phone2=$row['phone2'];
	//if($phone2){
		//$r.='<phone>'.$row['phone2'].'</phone>'."\r\n";
	//}
	//$r.=get_phone_agents($row['id_agent'],$row['phone'],$row['phone2'],$row['phone3']);//'<phone>'.$phone.'</phone>'."\r\n";
	$r.='<category>'.$type_seller.'</category>'."\r\n";
	$r.='<organization>'.$name.'</organization>'."\r\n";
	$r.='</sales-agent>'."\r\n";
	$r.='<price>'."\r\n";
	$r.='<value>'.$row['price']*$b. '</value>'."\r\n";
	$r.='<currency>'.$currency.'</currency>'."\r\n";
	$r.='</price>'."\r\n";
	$r.='<mortgage>'.$row['hypothec'].'</mortgage>'."\r\n";
	if($row['id_type_flat']=="2"){
		$r.='<deal-status>'.'прямая продажа'.'</deal-status>'."\r\n";
	}
	else if($row['id_type_flat']=="7"){
		$r.='<deal-status>'.'размен'.'</deal-status>'."\r\n";
	}
	else if($row['id_type_flat']=="8"){
        $r.='<deal-status>'.'встречная продажа'.'</deal-status>'."\r\n";
	}
	$r.='<description>'."\r\n";
	$r.=htmlspecialchars($row['comment_for_clients'])."\r\n";
	$r.='</description>'."\r\n";
	$r.=get_foto_yandex_flats($row['id'],1); //В качестве аргумента пишем 1 -т.е. квартиры.
	$repair=$row['str_repair'];
	if($repair){
		$r.='<renovation>'.$row['str_repair'].'</renovation>'."\r\n";
	}
	
	//если общая площадь каким-то чудом не указана,будет указано значение 0
	$x=$row['s_all'] ;
	if($x==true){
     $r.='<area>'."\r\n";
	$r.='<value>'.$row['s_all'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	}
	else{
	$r.='<area>'."\r\n";
	$r.='<value>'.'0'.'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	}
	
	$r.='</area>'."\r\n";
	//следующие три тэга ставятся только при наличии значений в БД

	$x=$row['s_life'];
	if($x){
	$r.='<living-space>'."\r\n";
	$r.='<value>'.$row['s_life'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</living-space>'."\r\n";
	}
	//костыль для убирания скобок и другого треша в строке метража комнат
	$s_rooms=$row['s_rooms'];

	$s_rooms_fixed = str_replace("(","",$s_rooms);
	$s_rooms_fixed = str_replace(")","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace(" ","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("/","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("и","+",$s_rooms_fixed);
	//разбиваем с помощью функции explode площадь комнат на отдельные тэги,"+" является разделителем,далее 
//с помощью цикла проставляем необходимое количество тэгов(сколько значений в виде массива вернет функция explode ,столько тэгов <room-space> и будет)	
	$array_s_rooms=explode("+",$s_rooms_fixed);
	$i=0;
	if($s_rooms){	
		while ($i<count($array_s_rooms)){
	$r.='<room-space>'."\r\n";
    $r.='<value>'.$array_s_rooms[$i].'</value>'."\r\n";
    $r.='<unit>'.$unit.'</unit>'."\r\n";
    $r.='</room-space>'."\r\n";
    $i++;

	    }
	 }

	$x=$row['s_kitchen'];
	if($x){
	$r.='<kitchen-space>'."\r\n";
	$r.='<value>'.$row['s_kitchen'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</kitchen-space>'."\r\n";
	}
	$r.='<rooms>'.$row['amount_rooms'].'</rooms>'."\r\n";




	$r.='<rooms-type>'.$row['str_flat_type'].'</rooms-type>'."\r\n";
	$r.= true_false_yandex('phone',$row['str_phone']);
	$r.= balcony_fix($row['id_balcony']);
	$bathroom=$row['str_bathroom'];
	if($bathroom){
		$r.='<bathroom-unit>'.$row['str_bathroom'].'</bathroom-unit>'."\r\n";
	}
	$floor=$row['str_floor_material'];
	if($floor){
		$r.='<floor-covering>'.$row['str_floor_material'].'</floor-covering>'."\r\n";
	}
	$window=$row['str_view_from_window'];
	if($window){
		$r.='<window-view>'.$row['str_view_from_window'].'</window-view>'."\r\n";
	}
	$r.='<floor>'.$row['floor'].'</floor>'."\r\n";
	$r.='<floors-total>'.$row['floor_all'].'</floors-total>'."\r\n";
	$building_type=$row['str_type_house_material'];
	if($building_type){
		$r.='<building-type>'.$row['str_type_house_material'].'</building-type>'."\r\n";
	}
	$building_series=$row['str_type_house'];
	if($building_series){
		$r.='<building-series>'.$row['str_type_house'].'</building-series>'."\r\n";
	}
	$s_ceiling=$row['s_ceiling'];
	if($s_ceiling){
	$r.='<ceiling-height>'.$row['s_ceiling'].'</ceiling-height>'."\r\n";
}
	$r.='</offer>'."\r\n";
	
}

     echo "квартиры записаны \r\n";
	 
	//дописываем комнаты
	$category='комната';
	//Делаем необходимый запрос активных комнат к mysql,помещаем извлеченные данные в ассоциативный массив,c помощью цикла начинаем запись.
$res=$db ->query("SELECT *  FROM `gcn_rooms`,`gcn_rooms_ext`,`gcn_kadr` WHERE  gcn_rooms.id_user=gcn_kadr.id_agent  and gcn_rooms.id = gcn_rooms_ext.id and gcn_rooms.objects_status ='0' and gcn_rooms_ext.adv_for_dk = 1 AND gcn_rooms.removal_request = '1'");
while($row=$res ->fetch_assoc())
{
	$r.='<offer internal-id'.'="'.$row['id'].'">'."\r\n";
	$r.='<type>'.$type.'</type>'."\r\n";
	$r.='<property-type>'.$property_type.'</property-type>'."\r\n";
	$r.='<category>'.$category.'</category>'."\r\n";
	$r.='<url>'.'http://www.gcn-spb.ru/komnaty/details.php?t=room&amp;id='.$row['id'].'</url>'."\r\n";
	$r.='<creation-date>'.get_Datetime_ISO8601($row['date_edit']).'</creation-date>'."\r\n";
	if($row['date_edit'])
	$r.='<last-update-date>'.get_Datetime_ISO8601($row['date_update_emls']).'</last-update-date>'."\r\n";
		$r.='<location>'."\r\n";
	$r.='<country>'.$country.'</country>'."\r\n";
	$r.='<region>'.$row['str_reg'].'</region>'."\r\n";
	if($row['id_reg'] != '2'){
		$district=str_replace("пгт.","поселок городского типа",$row['str_district']);
		$r.='<district>'.$district.'</district>'."\r\n";
	}	
		$locality_name=str_replace("пгт.","поселок городского типа",$row['str_dept']);
		$r.='<locality-name>'.$locality_name.'</locality-name>'."\r\n";
		if($row['id_reg'] == '2'){
		$sub_locality_name=$row['str_district'];
		$sub_locality_name=str_replace("пгт.","поселок городского типа",$sub_locality_name);
	    $r.='<sub-locality-name>'.$sub_locality_name.'</sub-locality-name>'."\r\n";
	}

	$street=$row['str_street'];
	$street=str_replace("бул.","бульвар", $street);
	$street=str_replace("Кр.Село","Красное Село", $street);
	$street=str_replace("шос.","шоссе", $street);
	$street=str_replace("кан. наб.","канала набережная",$street);
	$row['str_street']=$street;
	$corpus=$row['house_korpus'];
	if($corpus){
		$r.='<address>'.$row['str_street'].','.$row['house_number'].',к.'.$corpus.'</address>'."\r\n";
	}
	else {
		$r.='<address>'.$row['str_street'].','.$row['house_number'].'</address>'."\r\n";
	}
	$r.='<metro>'."\r\n";
	$r.='<name>'.$row['str_metro'].'</name>'."\r\n";
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'пеш')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);	
	$r.='<time-on-foot>'.$matches[0][0].'</time-on-foot>'."\r\n";
	}
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'тр')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);
	$r.='<time-on-transport>'.$matches[0][0].'</time-on-transport>'."\r\n";
	}
	$r.='</metro>'."\r\n";
	$r.='</location>'."\r\n";
	$r.='<sales-agent>'."\r\n";
	$r.='<name>'.$row['str_user'].'</name>'."\r\n";
	$r.='<phone>'.$row['phone'].'</phone>'."\r\n";
	//$r.='<phone>'.$row['phone2'].'</phone>'."\r\n";
	$r.='<category>'.$type_seller.'</category>'."\r\n";
	$r.='<organization>'.$name.'</organization>'."\r\n";
	$r.='</sales-agent>'."\r\n";
	$r.='<price>'."\r\n";
	$r.='<value>'.$row['price']*$b. '</value>'."\r\n";
	$r.='<currency>'.$currency.'</currency>'."\r\n";
	$r.='</price>'."\r\n";
	$r.='<mortgage>'.$row['hypothec'].'</mortgage>'."\r\n";
	if($row['id_type_flat']=="2"){
		$r.='<deal-status>'.'прямая продажа'.'</deal-status>'."\r\n";
	}
	else if($row['id_type_flat']=="7"){
		$r.='<deal-status>'.'размен'.'</deal-status>'."\r\n";
	}
	else if($row['id_type_flat']=="8"){
        $r.='<deal-status>'.'встречная продажа'.'</deal-status>'."\r\n";
	}
	$r.='<description>'."\r\n";
	$r.=htmlspecialchars($row['comment_for_clients'])."\r\n";
	$r.='</description>'."\r\n";
	$r.=get_foto_yandex_room($row['id'],2); //В качестве аргумента пишем 2 -т.е. комнаты.

    $x=$row['s_all'] ;
	if($x==true){
    $r.='<area>'."\r\n";
	$r.='<value>'.$row['s_all'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	}
	else{
	$r.='<area>'."\r\n";
	$r.='<value>'.'0'.'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	}
	
	$r.='</area>'."\r\n";

    
	$x=$row['s_life'];
	if($x){
	$r.='<living-space>'."\r\n";
	$r.='<value>'.$row['s_life'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</living-space>'."\r\n";
	}
		$s_rooms=$row['s_rooms'];

	$s_rooms_fixed = str_replace("(","",$s_rooms);
	$s_rooms_fixed = str_replace(")","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace(" ","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("/","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("и","+",$s_rooms_fixed);
	$array_s_rooms=explode("+",$s_rooms_fixed);
	$i=0;
	if($s_rooms){	
		while ($i<count($array_s_rooms)){
	$r.='<room-space>'."\r\n";
    $r.='<value>'.$array_s_rooms[$i].'</value>'."\r\n";
    $r.='<unit>'.$unit.'</unit>'."\r\n";
    $r.='</room-space>'."\r\n";
    $i++;

	    }
	 }

    $x=$row['s_kitchen'];
	if($x){
	$r.='<kitchen-space>'."\r\n";
	$r.='<value>'.$row['s_kitchen'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</kitchen-space>'."\r\n";
    }
	$r.='<rooms-offered>'.$row['amount_rooms'].'</rooms-offered>'."\r\n";
	$r.='<rooms>'.$row['amount_rooms_total'].'</rooms>'."\r\n";
	$r.='<rooms-type>'.$row['str_flat_type'].'</rooms-type>'."\r\n";
	$r.= true_false_yandex('phone',$row['str_phone']);
	$r.= balcony_fix($row['id_balcony']);
	$bathroom=$row['str_bathroom'];
	if($bathroom){
		$r.='<bathroom-unit>'.$row['str_bathroom'].'</bathroom-unit>'."\r\n";
	}
	$floor=$row['str_floor_material'];
	if($floor){
		$r.='<floor-covering>'.$row['str_floor_material'].'</floor-covering>'."\r\n";
	}
	$window=$row['str_view_from_window'];
	if($window){
		$r.='<window-view>'.$row['str_view_from_window'].'</window-view>'."\r\n";
	}
	$r.='<floor>'.$row['floor'].'</floor>'."\r\n";
	$r.='<floors-total>'.$row['floor_all'].'</floors-total>'."\r\n";
	$building_type=$row['str_type_house_material'];
	if($building_type){
		$r.='<building-type>'.$row['str_type_house_material'].'</building-type>'."\r\n";
	}
	$building_series=$row['str_type_house'];
	if($building_series){
		$r.='<building-series>'.$row['str_type_house'].'</building-series>'."\r\n";
	}
	$s_ceiling=$row['s_ceiling'];
	if($s_ceiling){
	$r.='<ceiling-height>'.$row['s_ceiling'].'</ceiling-height>'."\r\n";
}
	$r.='</offer>'."\r\n";
	
	
	
	
	
	
	
}

		echo "комнаты записаны \r\n";
	
	//дописываем загородную недвижимость
	//Делаем необходимый запрос активных объектов загородной недвижимости к mysql,помещаем извлеченные данные в ассоциативный массив,c помощью цикла начинаем запись.
	
	$res=$db ->query("SELECT *  FROM `gcn_farm`,`gcn_farm_ext`,`gcn_kadr` WHERE  gcn_farm.id_user=gcn_kadr.id_agent  and gcn_farm.id = gcn_farm_ext.id and gcn_farm.objects_status ='0' and gcn_farm_ext.adv_for_dk = 1 AND gcn_farm.removal_request = '1'");
	while($row=$res ->fetch_assoc()){
		$r.='<offer internal-id'.'="'.$row['id'].'">'."\r\n";
		$r.='<type>'.$type.'</type>'."\r\n";
		$r.='<property-type>'.$property_type.'</property-type>'."\r\n";
		$r.=obj_type_fix_farm($row['id_obj_type']);
		$r.='<url>'.'http://www.gcn-spb.ru/zagorodnaya-nedvizhimost/details.php?t=farm&amp;id='.$row['id'].'</url>'."\r\n";
		if(!empty($row['cadastral_number']))
			$r.='<cadastral-number>'.htmlspecialchars($row['cadastral_number']).'</cadastral-number>'."\r\n";
		$r.='<creation-date>'.get_Datetime_ISO8601($row['date_edit']).'</creation-date>'."\r\n";
			if($row['date_edit'])
	    $r.='<last-update-date>'.get_Datetime_ISO8601($row['date_update_emls']).'</last-update-date>'."\r\n";
	   	$r.='<location>'."\r\n";
	$r.='<country>'.$country.'</country>'."\r\n";
	$r.='<region>'.$row['str_reg'].'</region>'."\r\n";
	if($row['id_reg'] != '2'){
		$district=str_replace("пгт.","поселок городского типа",$row['str_district']);
		$r.='<district>'.$district.'</district>'."\r\n";
	}	
		$locality_name=str_replace("пгт.","поселок городского типа",$row['str_dept']);
		$r.='<locality-name>'.$locality_name.'</locality-name>'."\r\n";
		if($row['id_reg'] == '2'){
		$sub_locality_name=$row['str_district'];
		$sub_locality_name=str_replace("пгт.","поселок городского типа",$sub_locality_name);
	    $r.='<sub-locality-name>'.$sub_locality_name.'</sub-locality-name>'."\r\n";
	}

	$street=$row['str_street'];
	$street=str_replace("бул.","бульвар", $street);
	$street=str_replace("Кр.Село","Красное Село", $street);
	$street=str_replace("шос.","шоссе", $street);
	$street=str_replace("кан. наб.","канала набережная",$street);
	$row['str_street']=$street;
	$corpus=$row['house_korpus'];
	if($corpus){
		$r.='<address>'.$row['str_street'].','.$row['house_number'].',к.'.$corpus.'</address>'."\r\n";
	}
	else {
		$r.='<address>'.$row['str_street'].','.$row['house_number'].'</address>'."\r\n";
	}
	$r.='<metro>'."\r\n";
	$r.='<name>'.$row['str_metro'].'</name>'."\r\n";
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'пеш')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);	
	$r.='<time-on-foot>'.$matches[0][0].'</time-on-foot>'."\r\n";
	}
	if(!empty($row['str_metro_transport']) && false !== strpos($row['str_metro_transport'],'тр')){
	preg_match_all('|\d+|',$row['str_metro_transport'], $matches);
	$r.='<time-on-transport>'.$matches[0][0].'</time-on-transport>'."\r\n";
	}
	$r.='</metro>'."\r\n";
	$r.='</location>'."\r\n";
		$r.='<sales-agent>'."\r\n";
		$r.='<name>'.$row['str_user'].'</name>'."\r\n";
	    $r.='<phone>'.$row['phone'].'</phone>'."\r\n";
	   // $phone2=$row['phone2'];
	   // if($phone2){
		//$r.='<phone>'.$row['phone2'].'</phone>'."\r\n";
	//}
	    $r.='<category>'.$type_seller.'</category>'."\r\n";
	    $r.='<organization>'.$name.'</organization>'."\r\n";
	    $r.='</sales-agent>'."\r\n";
		$r.='<price>'."\r\n";
	    $r.='<value>'.$row['price']*$b. '</value>'."\r\n";
	    $r.='<currency>'.$currency.'</currency>'."\r\n";
	    $r.='</price>'."\r\n";
		$r.='<mortgage>'.$row['hypothec'].'</mortgage>'."\r\n";
		$r.='<description>'."\r\n";
	    $r.=htmlspecialchars($row['comment_for_clients'])."\r\n";
	    $r.='</description>'."\r\n";
		if($row['str_ready'])
		$r.='<renovation>'.$row['str_ready'].'</renovation>'."\r\n";
		$r.=get_foto_yandex_farm($row['id'],3); //В качестве аргумента пишем 3 -т.е. загород.
		//если общая площадь каким-то чудом не указана,будет указано значение 0
	$x=$row['s_total'] ;
	$id_obj_type = $row['id_obj_type'];
	if(!empty($x) and $id_obj_type != '4'){
     $r.='<area>'."\r\n";
	$r.='<value>'.$row['s_total'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</area>'."\r\n";
	}
	else if(empty($x) and $id_obj_type != '4' ){
	$r.='<area>'."\r\n";
	$r.='<value>'.'0'.'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</area>'."\r\n";
	}
	$x=$row['s_rooms'];
	if($x){
	$r.='<living-space>'."\r\n";
	$r.='<value>'.$row['s_rooms'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</living-space>'."\r\n";
	}
	//костыль для убирания скобок и другого треша в строке метража комнат
	$s_rooms=$row['str_s_rooms'];

	$s_rooms_fixed = str_replace("(","",$s_rooms);
	$s_rooms_fixed = str_replace(")","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace(" ","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("/","",$s_rooms_fixed);
	$s_rooms_fixed = str_replace("и","+",$s_rooms_fixed);
	//разбиваем с помощью функции explode площадь комнат на отдельные тэги,"+" является разделителем,далее 
//с помощью цикла проставляем необходимое количество тэгов(сколько значений в виде массива вернет функция explode ,столько тэгов <room-space> и будет)	
	$array_s_rooms=explode("+",$s_rooms_fixed);
	$i=0;
	if($s_rooms){	
		while ($i<count($array_s_rooms)){
	$r.='<room-space>'."\r\n";
    $r.='<value>'.$array_s_rooms[$i].'</value>'."\r\n";
    $r.='<unit>'.$unit.'</unit>'."\r\n";
    $r.='</room-space>'."\r\n";
    $i++;

	    }
	 }
	$x=$row['s_kitchen'];
	if($x){
	$r.='<kitchen-space>'."\r\n";
	$r.='<value>'.$row['s_kitchen'].'</value>'."\r\n";
	$r.='<unit>'.$unit.'</unit>'."\r\n";
	$r.='</kitchen-space>'."\r\n";
    }
	if($row['area']){
	$r.='<lot-area>'."\r\n";
	$r.='<value>'.$row['area'].'</value>'."\r\n";
	$r.='<unit>'.'сот'.'</unit>'."\r\n";
	$r.='</lot-area>'."\r\n";
	}
	//этажность
		if($row['floors']){
	$r.='<floors-total>'.$row['floors'].'</floors-total>'."\r\n";
	}
	$str_type_property=$row['str_type_property'];
	$str_type_property=str_replace("Сад-во","садоводство",$str_type_property);
	$row['str_type_property']=$str_type_property;
	if($row['str_type_property'])
    $r.='<lot-type>'.$row['str_type_property'].'</lot-type>'."\r\n";
    $r.=true_false_yandex('phone',$row['str_phones']);
	$r.=toilet_fix($row['id_obj_type']);
	if(!empty($row['amount_rooms']))
	$r.='<rooms>'.$row['amount_rooms'].'</rooms>'."\r\n";

    $id_head=$row['id_head'];
	if($id_head>2)
    $r.='<heating-supply>'.'да'.'</heating-supply>'."\r\n";
    else if($id_head==2)
	$r.='<heating-supply>'.'нет'.'</heating-supply>'."\r\n";
    
    $id_water=$row['id_water'];
	if($id_water==5)
    $r.='<water-supply>'.'нет'.'</water-supply>'."\r\n";
     else if($id_water==1);
     else if (empty($id_water));
		 else
			 $r.='<water-supply>'.'да'.'</water-supply>'."\r\n";
    
	$id_electro=$row['id_electro'];
		if($id_electro==2)
    $r.='<electricity-supply>'.'нет'.'</electricity-supply>'."\r\n";
     else if($id_electro==1);
     else if (empty($id_electro));
		 else
			 $r.='<electricity-supply>'.'да'.'</electricity-supply>'."\r\n";
		
    $id_gas=$row['id_gas'];
	     if($id_gas==2)
	$r.='<gas-supply>'.'нет'.'</gas-supply>'."\r\n";
     else if($id_gas==1);
     else if (empty($id_gas));
		 else
			 $r.='<gas-supply>'.'да'.'</gas-supply>'."\r\n";
		   
	$r.='</offer>'."\r\n";
	    
	    	
		
	}
	
	
	
		echo "загородная недвижимость записана \r\n";	 
	 
	 


$r.='</realty-feed>'."\r\n";
$row=$r;
if($row)
    echo "Запись завершена.\r\n Готовый xml-фид доступен по ссылке www.325-75-85.ru/realty-yandex/domclick.xml \r\n";
file_put_contents($file, $row);
?>