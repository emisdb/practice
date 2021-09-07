<?php
error_reporting(E_ALL);
echo "Script is running\n";
require_once('functions_converter_bkn_prof.php');
require_once('functions_converter_cian.php');
$config = require_once('config.php');
$lists = require_once('lists.php');
//создаем экземпляр класса DomDocument
$document= new DomDocument ('1.0','utf-8');
$db=new mysqli('localhost','root','password','gcn');
$db->set_charset('utf8');
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}
$doc_feed = $document->appendChild($document->createElement('feed'));
$feedver = $document->createElement('feed_version');
$feedver->appendChild($document->createTextNode('2'));
$doc_feed->appendChild($feedver);

//$arr_type = $config['flat_sale'];
//$arr_tables = array_keys($arr_type);

foreach($config as $block_type => $arr_type){
//	var_dump($arr_type);
	$query = query_builder($arr_type);
//	echo "Q: ".$query."\n";
//	continue;

	//gcn.gcn_flats_ext.adv_for_emls =1 AND removal_request = '1';";
//echo "Q:".$query;// exit();
	$result_flats_table=$db->query($query);

	if (!$result_flats_table) {
		echo "Невозможно выполнить запрос gcn_flats из БД: " . mysqli_error($db);
		exit;
	}
	if ($result_flats_table->num_rows == 0) {
		echo "Запрос к списку {} вернул 0 строк, выполнение прервано!";
	}

	$nr = $result_flats_table->num_rows;
	echo "NR:" . $nr . "\n";
	while ($row_res = $result_flats_table->fetch_assoc()) {

		$object = $doc_feed ->appendChild($document->createElement('object'));
//	$object->appendChild($document->createElement("id",$i++));
		workflow($arr_type,$row_res,$object,$document,$lists);
		$query = query_builder_photo($block_type, $row_res['id']);
		$result_photo_table=$db->query($query);
		$nom =0;
		while ($row_ph = $result_photo_table->fetch_assoc()) {
			if($nom==0){
				$photos = $object->appendChild($document->createElement('Photos'));
			}
			$photo= $photos->appendChild($document->createElement('PhotoSchema'));
			$str_foto = 'http://agent.gcn-spb.ru/agent/foto'.$row_ph['photo_file'];
			$photo->appendChild($document->createElement('FullUrl',$str_foto));
			if($nom==0){
				$photo->appendChild($document->createElement('IsDefault',1));
			}
			$nom++;
		}

	}
}


//генерация xml
$document->formatOutput = true; // установка атрибута formatOutput
// domDocument в значение true


$file = $document->save('cian_test.xml'); // сохранение файла
if($file)
	echo "complete. \r\n ";
else echo "some error";