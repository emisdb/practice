<?php
$emls_prefix=array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'A','11'=>'B','12'=>'C','13'=>'D','14'=>'E','15'=>'F','16'=>'G','17'=>'H','18'=>'I','19'=>'J','20'=>'K','21'=>'L','22'=>'M','23'=>'N','24'=>'O','25'=>'P');
function make_emls_date ($date_in, $date_type)
{//������ ������� ���������� ���� � ������� ���� � ������ ��.��.����, ������ ���������� ���� �������, ������ ��� ����. 0 ���� � ������� unix, 1 - ���� � ������� ����-��-��, 3 - ���� ������ � ��������� ��������� ��� ������������ ������������.
//��������� �������� �������� ����?
if ($date_type==1)
{
 $date_elements=explode("-",$date_in);
 return $date_elements[2]. "." . $date_elements[1]. "." . $date_elements[0];
}
if ($date_type==3)
{
 if ($date_in=="01.01.0001")
 	{return "";}
 else
 	{return $date_in ;}
}

}
function if_null($int_in)
{//������ ������� ���������� 0, ���� �������� ���������� ������ ��� null ����� ���������� ����������
if($int_in=="") {return 0;}
return $int_in;
}
function if_null_sprav($codesprav_in)
{//������ ������� ���������� 1, ���� �� ��������� ���� �������������� �� ����������� ��� ��������� 0 ��� ""
if ($codesprav_in=="" or $codesprav_in==0) {return 1;}
else {return $codesprav_in;}
}
function make_foto_emls_string($id_obj, $id_base)
{//������ ������� ��������� ������ ���������� ��� ���������� � ����, id_base ������� �� gcn_list_type_base
global $emls_prefix;
$foto_query="SELECT photo_file FROM gcn_foto WHERE id_object=$id_obj AND id_base=$id_base AND photo_status=1 ORDER BY photo_sorting ASC;" ;
$result_foto_table=mysql_query($foto_query);
if (!$result_foto_table)
	{
		echo "���������� ��������� ������ ($foto_query) �� ��: " . mysql_error();
        exit;
    }
//���� ���������� �� �������, ���������� ������ ������
if (mysql_num_rows($result_foto_table) == 0) {return "";}
//������� ������ ������ �� ����������
$row_foto_string=mysql_fetch_assoc($result_foto_table);
$x=1;
$foto_string=$emls_prefix[$x].'-'.substr($row_foto_string['photo_file'],1). ";" ;
//������ �������� ��������� ������ �� ����, ���� ��� ����
$x=2;
while($row_foto_string=mysql_fetch_assoc($result_foto_table))
{
$foto_string=$foto_string.$emls_prefix[$x].'-'.substr($row_foto_string['photo_file'],1). ";" ;
//echo$emls_prefix[$x].'-';
$x++;
}
mysql_free_result($result_foto_table);
//echo $foto_string , "<br>";
return $foto_string;
}
function make_metro_how_get($metro_how_get_in)
{//������ ������� ���������� 3 �������� - ��� ������� ��������� �� �����, ����������, ����� �� ��������� ������� �����, ������� ��������� - ������, ��������� � �.�.
 $metro_how_get_query="SELECT * FROM gcn_list_metro_how_get WHERE id_item=$metro_how_get_in;" ;
$result_metro_how_get=mysql_query($metro_how_get_query);
if (!$result_metro_how_get)
	{
		echo "���������� ��������� ������ ($metro_how_get_query) �� ��: " . mysql_error();
        exit;
    }
//���� ������������ �� �������, ���������� ��������� ������
if (mysql_num_rows($result_metro_how_get) == 0) {return array (1, "", "");}
$row_metro_how_get=mysql_fetch_assoc($result_metro_how_get);
//������� ����� ���������� ������
mysql_free_result($result_metro_how_get);
return array($row_metro_how_get['emls_offline_kmtype'], $row_metro_how_get['emls_offline_kmtime'], $row_metro_how_get['emls_offline_munit']);
}

function replace_rn ($string_in)
{//������ ������� ��������������� ������� ������, ������� ��� ������ ����� ������ � ������
$replacement=array("\r\n", "\n", "\r");
$replace=" ";
return str_replace($replacement, $replace, $string_in);
}

function make_kn_price ($id_price_in)
{//������ ������� ���������� ������ � �������, � ����������� �� �������������� ���� ���� ������������ ������������
 $kn_price_query="SELECT word FROM r_list_kn_prices WHERE id=$id_price_in;" ;
$result_kn_price_query=mysql_query($kn_price_query);
if (!$result_kn_price_query)
	{
		echo "���������� ��������� ������ ($kn_price_query) �� ��: " . mysql_error();
        exit;
    }
//���� ������������ �� �������, ���������� ������ ������
if (mysql_num_rows($result_kn_price_query) == 0) {return "";}
$row_result_kn_price_query=mysql_fetch_assoc($result_kn_price_query);
$str_price_type=$row_result_kn_price_query['word'];
return $str_price_type;
}
function make_nls_builds($floor,$floors,$date_end){
	return $floor.';'.$floors.';'.date('d.m.Y',$date_end);
}

function get_bkn_prof_sex($gcn_sex)
{
// ������ ������� ���������� �������� ���� ������ � ������� ��� ����, ��� ���������� � ����������� gcn_list_sex
// � ������ ���� ���� �� ��������� �� ������������ ��� �������
	$gcn_sex_query="SELECT id_item_bkn_prof from gcn_list_sex WHERE id_item=" . $gcn_sex . ";" ;
	if (mysql_num_rows(mysql_query($gcn_sex_query)) == 0) {return "1";}
	$bkn_sex_row=mysql_fetch_assoc(mysql_query($gcn_sex_query));
	$bkn_sex=$bkn_sex_row['id_item_bkn_prof'];
	return $bkn_sex;
}

function get_bkn_prof_reg($gcn_reg)
{
// ������ ������� ���������� �������� ������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_reg
	$gcn_reg_query="SELECT bkn_prof_id_item from gcn_list_reg WHERE id_item=" . $gcn_reg . ";" ;
	$bkn_reg_row=mysql_fetch_assoc(mysql_query($gcn_reg_query));
	$bkn_reg=$bkn_reg_row['bkn_prof_id_item'];
	return $bkn_reg;
}
function get_bkn_prof_reg_dept($gcn_reg){
	
// ������ ������� ���������� �������� ������ ������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_reg_dept
   $gcn_reg_query="SELECT bkn_prof_id_item from gcn_list_reg_dept WHERE id_item=" . $gcn_reg . ";" ;
   $bkn_reg_row=mysql_fetch_assoc(mysql_query($gcn_reg_query));
   $bkn_reg=$bkn_reg_row['bkn_prof_id_item'];
   return $bkn_reg;
   
}
function get_bkn_prof_reg_dept_dist($gcn_reg){
   // ������ ������� ���������� �������� ������ ������ � ������� ��� ����, ��� ���������� � ����������� gcn_list_reg_dept_dist
   $gcn_reg_query="SELECT bkn_prof_id_district from gcn_list_reg_dept_dist WHERE id_reg_dept_dist=" . $gcn_reg . ";" ;
   $bkn_reg_row=mysql_fetch_assoc(mysql_query($gcn_reg_query));
   $bkn_reg=$bkn_reg_row['bkn_prof_id_district'];
   return $bkn_reg;
}
function get_bkn_prof_metro($id_metro){
  // ������ ������� ���������� �������� ����� � ������� ��� ����, ��� ���������� � ����������� gcn_list_metro
  $gcn_metro_query = "SELECT id_bkn_prof from gcn_list_metro WHERE id =".$id_metro.";";
  $bkn_metro_row = mysql_fetch_assoc(mysql_query($gcn_metro_query));
  $bkn_metro = $bkn_metro_row['id_bkn_prof'];
  return $bkn_metro;
  }
 function get_bkn_prof_metro_how_get($id_item){
 // ������ ������� ���������� �������� �������������� ��� ��������� �� ����� � ������� ��� ����, ��� ���������� � ����������� gcn_list_metro_how_get
 $gcn_metro_query = "SELECT bkn_prof_id_item_style from gcn_list_metro_how_get WHERE id_item =".$id_item.";";
 $bkn_metro_row = mysql_fetch_assoc(mysql_query($gcn_metro_query));
 $bkn_metro = $bkn_metro_row['bkn_prof_id_item_style'];
 return $bkn_metro;
	 
 } 
 function get_bkn_prof_fromSubway($id_item){
// ������ ������� ���������� �������� �������������� ������� � ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_metro_how_get 
 $gcn_metro_query = "SELECT bkn_prof_fromSubway from gcn_list_metro_how_get WHERE id_item =".$id_item.";";
 $bkn_metro_row = mysql_fetch_assoc(mysql_query($gcn_metro_query));
 $bkn_metro = $bkn_metro_row['bkn_prof_fromSubway'];
 return $bkn_metro;
 }
/*function get_bkn_prof_agreement($id_item){
//������ ������� ���������� �������� �������������� �������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_agreement
$agreement_query = "SELECT id_item_bkon_prof from gcn_list_type_agreement WHERE id_item =".$id_item." AND NOT NULL or '0';";
$agreement_row = mysql_fetch_assoc(mysql_query($agreement_query));
$agreement_status_bkn = $agreement_row['id_item_bkon_prof'];
return $agreement_status_bkn; 
	
} */
function get_bkn_prof_planning($id_item){
	// ������ ������� ���������� �������� �������������� ���������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_flat
$query = "SELECT bkn_prof_planning from gcn_list_type_flat WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_planning'];
return $result;
}
function get_bkn_prof_hotwater($id_item){
	 // ������ ������� ���������� �������� �������������� ������� ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_hotwater
$query = "SELECT bkn_prof_hotwater from gcn_list_hotwater WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_hotwater'];
return $result; 

}
function get_bkn_prof_enterhouse($id_item){
	// ������ ������� ���������� �������� �������������� ����� � ��� � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_enter
$query = "SELECT bkn_prof_type_enter from gcn_list_type_enter WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_type_enter'];
return $result; 
}
function get_bkn_prof_housetype($id_item){
	// ������ ������� ���������� �������� �������������� ���� ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_house
$query = "SELECT bkn_prof_list_type_house from gcn_list_type_house WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_type_house'];
return $result; 
}
function get_bkn_prof_stove_plate($id_item){
	// ������ ������� ���������� �������� �������������� �������� ����� � ������� ��� ����, ��� ���������� � ����������� gcn_list_stove_plate
$query = "SELECT bkn_prof_list_stove from gcn_list_stove_plate WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_stove'];
return $result; 
}
function get_bkn_prof_deal_type($id_item){
	// ������ ������� ���������� �������� �������������� ���� ������ � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_deal
$query = "SELECT bkn_prof_list_type_deal from gcn_list_type_deal WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_type_deal'];
return $result; 
}
function get_bkn_prof_list_phone($id_item){
	// ������ ������� ���������� �������� �������������� ������� �������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_phone
$query = "SELECT bkn_prof_list_phone from gcn_list_phone WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_phone'];
return $result; 
}
function get_bkn_prof_list_material($id_item){
	// ������ ������� ���������� �������� �������������� ��������� ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_type_house_material
$query = "SELECT bkn_prof_list_material from gcn_list_type_house_material WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_material'];
return $result; 
}
function get_bkn_prof_list_repair($id_item){
	// ������ ������� ���������� �������� �������������� ������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_repair
$query = "SELECT bkn_prof_list_repair from gcn_list_repair WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_repair'];
return $result; 
}
function get_bkn_prof_list_bathroom($id_item){
	// ������ ������� ���������� �������� �������������� ������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_bathroom
$query = "SELECT bkn_prof_list_bathroom from gcn_list_bathroom WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_bathroom'];
return $result; 
}
function get_bkn_prof_list_balcony($id_item){
	// ������ ������� ���������� �������� �������������� ������� � ������� ��� ����, ��� ���������� � ����������� gcn_list_balcony
$query = "SELECT bkn_prof_list_balcony from gcn_list_balcony WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_balcony'];
return $result; 
}
function get_bkn_prof_list_floor_type($id_item){
	// ������ ������� ���������� �������� �������������� ���� ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_floor_material
$query = "SELECT bkn_prof_list_floor_material from gcn_list_floor_material WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_floor_material'];
return $result; 
}
function get_bkn_prof_list_lift($id_item){
	// ������ ������� ���������� �������� �������������� ����� � ������� ��� ����, ��� ���������� � ����������� gcn_list_lift
$query = "SELECT bkn_prof_list_lift from gcn_list_lift WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_lift'];
return $result; 
}
function get_bkn_prof_list_window_view($id_item){
	// ������ ������� ���������� �������� �������������� ���� �� ���� � ������� ��� ����, ��� ���������� � ����������� gcn_list_view_from_window
$query = "SELECT bkn_prof_list_view_from_window from gcn_list_view_from_window WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_list_view_from_window'];
return $result; 
	
	
}
function get_bkn_prof_objType($id_item){
	// ������ ������� ���������� �������� �������������� ���� ������� �������. ������������ � ������� ��� ����, ��� ���������� � �����������
$query = "SELECT bkn_prof_id_item from gcn_list_comm_use WHERE id_item =".$id_item.";";	
if(mysql_query($query)){
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_id_item'];
return $result; }
	return 0;
		
}
function get_bkn_prof_price_type($id_item){
	// ������ ������� ���������� �������� �������������� ���� ���� �������. ������������ � ������� ��� ����, ��� ���������� � �����������
$query = "SELECT bkn_prof from r_list_kn_prices WHERE id =".$id_item.";";	
if(mysql_query($query)){
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof'];
if(!empty($result))
return $result;
 else 
	 return "1";}
		
}
function get_bkn_prof_stateObj_type($id_item){
	//������ ������� ���������� �������� �������������� ��������� �������. ������� � ������� ��� ����, ��� ���������� � �����������
$query = "SELECT bkn_prof_id from gcn_list_comm_type_cond WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_id'];
return $result; 	
}
function get_bkn_prof_comm_entry($id_item){
	//������ ������� ���������� �������� �������������� ����� � ��������� � ������� ��� ����, ��� ���������� � �����������
$query = "SELECT bkn_prof_id_entry from gcn_list_entry WHERE id =".$id_item.";";	
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_id_entry'];
return $result; 	
}
function get_bkn_prof_security($id_item){
	//������ ������� ���������� �������� �������������� ������ � ������� ��� ����, ��� ���������� � �����������
$query = "SELECT bkn_prof_id from gcn_list_security WHERE id_item =".$id_item.";";
$row = mysql_fetch_assoc(mysql_query($query));
$result = $row['bkn_prof_id'];
return $result; 	
	
}
//������� ��� ����������� ���� ���������� ���������� ���� ������������ � ������� emls
/*
		@void
*/
function getIdEmlsUse(){
	global $db;
	global $document;
	global $offer;
	global $row_commer;
	$array_postr = $row_commer['str_postr'];
	$array_postr = explode(",",$array_postr);
	$sql_in = "";
	$counter = 0;
	while($counter<count($array_postr)){
		settype($array_postr[$counter],"int");
		$sql_in.=$array_postr[$counter].',';
		$counter++;
	}
	$sql_in = substr($sql_in,0,-1);
	$query_select = "SELECT DISTINCT id_emls_use from gcn_list_comm_use";
	$query_where = "WHERE id_item IN ";
	$query_in = "($sql_in);";
	$query_string = $query_select." ".$query_where."".$query_in;
	$query = $query_string;
	//return $query;
	//$query = "SELECT DISTINCT id_emls_use from gcn_list_comm_use WHERE id_item IN ('{$row_commer['str_postr']}')";
	$res = $db->query($query);
	$possibleAssignment = $offer->appendChild($document->createElement('possible-assignment'));
	while($row = $res->fetch_assoc()){
	$result = $row['id_emls_use'];
	if($result){
	$value = $possibleAssignment->appendChild($document->createElement('value'));
	$value->appendChild($document->createTextNode($result));}
			
						
					 	
}
}

function get_emls_type_property($id_item){
	//������ ������� ���������� �������� ������ �������������� ���� ������� � ����
	global $db;
$query = "SELECT emls_new_value from gcn_list_type_object_comm WHERE id_item =".$id_item.";";
$row = $db->query($query)->fetch_assoc();
$result = $row['emls_new_value'];
return $result;
}

/*
 * ������ � ����
 *
 * */
function getQuality($id_repair){
    global $db;
    $query = "SELECT id_emls from gcn_list_repair WHERE id_item = $id_repair";
    $row = $db->query($query)->fetch_assoc();
    return $row['id_emls'];
}


?>