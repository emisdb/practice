<?php
error_reporting(E_ALL);
echo "Script is running\n";
require_once('functions_converter_bkn_prof.php');
require_once('functions_converter_cian.php');
$config = require_once('config.php');
//создаем экземпляр класса DomDocument
$document= new DomDocument ('1.0','utf-8');

$doc_feed = $document->appendChild($document->createElement('feed'));
$feedver = $document->createElement('feed_version');
$feedver->appendChild($document->createTextNode('2'));
$doc_feed->appendChild($feedver);
$arr_type = $config['flat_sale'];


	foreach ($arr_type as $k => $valu){
		if($k != 'gcn_kadr') {
			continue;
		}
		foreach ($valu as $key => $value) {
			if($key != 'Phones') {
				continue;
			}
			echo "1:$key > \n";

			foreach ($value[1] as $values) {
				foreach ($values[key($values)] as $kv => $vals) {
					echo "Q:".key($values)." : ".$kv." : ".$vals[0].".".$vals[1]."\n";
				}

			}
		}
	}
$arr_type = $config['flat_sale']['gcn_kadr']['Phones'];

$res =get_fields($arr_type,'gcn_kadr')	;
echo "Reso:".$res;

?>

