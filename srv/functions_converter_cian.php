<?php
/**
 * Основные Функции конвертора
 *
 * @package functions_convertor
 */

/**
 * Специфические значения от ЦИАН
 *
 * @param $value string
 * @param $field string
 * @return string
 */
function conv_cian($value, $field)
{
    switch ($field){
        case "FlatRoomsCount":
            $thisfield = $value;
            if($value=="Студия") return 9;
            if ($value>5) return 6 ;

            return $thisfield;
        case "Price":
            $thisfield = floatval($value);
            if ($thisfield>0) return $thisfield*1000 ;
            return 0;
        case "RoomArea":
            $strings = explode("+", $value );
            $result =0;
            foreach ($strings as $string){
                $result += (float)$string;
            }
            return $result;
        case "AllRoomsArea":
            if (!(strlen($value)>0)) return "";
            $strings = explode("+", $value );
            $combined =false;
            $result = array();
            foreach ($strings as $string){
                if(strpos($string, "(") === 0){
                    $string = rtrim(str_replace("(", "-", $string));
                    $combined = true;
                } else {
                    if(strpos($string, ")") > 0){
                        $string = rtrim(str_replace(")", "", $string));
                        $string = "-".$string;
                        $combined = false;
                    } else {
                        if($combined){
                            $string = "-".rtrim($string);
                        } else {
                            $string = "+".rtrim($string);
                        }
                    }
                 }
                $result[] = $string;
            }
            return implode("",$result);
        default:
            return htmlspecialchars($value);
    }
}

/**
 * Специфические значения от ЦИАН
 *
 * @param $value array
 * @param $field array
 * @return string
 */
function conv_complex($row, $fields){
    $result = "";
    foreach ($fields as $field){
        if (!empty($row[$field])) {
            switch ($field){
                case "id_reg":
                    if($row[$field] != 2) $result.= trim($row['str_reg'])." ";
                    break;
                case "str_reg":
                    break;
                case "house_number":
                    $result.= " д.".trim($row[$field])." ";
                    break;
                case "house_korpus":
                    $result.= " корпус ".trim($row[$field])." ";
                    break;
                case "flat_number":
 //                   $result.= " кв.".trim($row[$field])." ";
                    break;
                default:
                    $result.= trim($row[$field])." ";
            }
        }
    }
    return trim($result);
}

function get_fields($arr,$table){
    $result = array();
    foreach($arr as $field){
 //          var_dump($field);
        if (is_array($field)) {

            $res = get_fields($field,$table);
          if(is_array($res)){
              $result = array_merge($result,$res);
          } else {
              if(!empty($res)){
                  $result[]= $res;
              }
          }

        } else {
            if (count($arr)>1) {
                if ($field == 0){
                    return $table.".".$arr[1];
                } elseif ($field == 5){
                    return $table.".".$arr[2];
                } elseif ($field == 3){
                 $res = get_fields($arr[1],$table);
                    if(is_array($res)){
                        $result = array_merge($result,$res);
                    } else {
                        if(!empty($res)){
                            $result[]= $res;
                        }
                    }
                    return $result;
                }
            }
            return "";
        }
    }
    return $result;
}

function set_fields($key,$value,$row_res,$object,$document,$lists){
    $coll = array();
    $hasMain = 0;
    $coll = array();
    $parent = "";
//    $parent = $object->appendChild($document->createElement($key));
    foreach($value[1] as $values){
        $thisMain = 0;
        switch ($values[key($values)][0]){
            case 0:
                if($hasMain != 2){
                    if(!(empty($values[key($values)][2]))) {
                        $thisMain = 1;
                        $hasMain = 1;
                    }
                }
                //              echo "V:".$row_res['id'].":".$values[key($values)][1].":".$row_res[$values[key($values)][1]]."\n";
                if(!empty($row_res[$values[key($values)][1]])) {
                    if($thisMain == 1) $hasMain=2;
                   $coll[] = array(key($values),conv_cian($row_res[$values[key($values)][1]], key($values)));
//                    $parent->appendChild($document->createElement(key($values), conv_cian($row_res[$values[key($values)][1]], key($values))));
                }
                break;
            case 1:
                $coll[] = array(key($values),$values[key($values)][1]);
 //               $parent->appendChild($document->createElement(key($values), $values[key($values)][1]));
                break;
            case 3:
                $parent = $object->appendChild($document->createElement($key));
                set_fields(key($values),$values[key($values)],$row_res,$parent,$document,$lists);
                break;
            case 5:
                if(!empty($row_res[$values[key($values)][2]])){
                    if(!empty($lists[$values[key($values)][1]][$row_res[$values[key($values)][2]]])){
                        $coll[] = array(key($values),$lists[$values[key($values)][1]][$row_res[$values[key($values)][2]]]);
//                        $parent->appendChild($document->createElement(key($values),$lists[$values[key($values)][1]][$row_res[$values[key($values)][2]]]));
                    }
                }
                break;
        }
    }
     if($hasMain != 1) {
        if(empty($parent)){
            $parent = $object->appendChild($document->createElement($key));
        }
        foreach ($coll as $item) {
            $parent->appendChild($document->createElement($item[0], $item[1]));
        }
    }
}
/**
 * Специфические значения от ЦИАН
 *
 * @param $value array
 * @param $field array
 * @return string
 */
function query_builder($arr_type){
    $values = array();
    $from = "";
    $where = "";
    foreach ($arr_type as $v_table => $v_fields){
        if (empty($from)){
            $from = "gcn.".$v_table;
            $vv_table = $v_table;
        } else {
            if (empty($v_fields['config_from'])) {
                $id ='id';
                $realtab = $vv_table;
                $idd = 'id';
            } else {
                $id = $v_fields['config_from'][0];
                $realtab = $v_fields['config_from'][1];
                $idd = $v_fields['config_from'][2];
            }
            $from .= " INNER JOIN gcn.".$v_table." ON  gcn.{$v_table}.{$id} = gcn.{$realtab}.{$idd}";
        }
        if(!empty($v_fields['config_where'])){
            foreach ($v_fields['config_where'] as $v_field) {
                if (empty($where)){
                    $where = "gcn.{$v_table}.{$v_field[0]} = {$v_field[1]}";
                } else {
                    $where.= " AND gcn.{$v_table}.{$v_field[0]} = {$v_field[1]}";
                }

            }
        }
        $whereor ="";
        if(!empty($v_fields['config_where_or'])){
            foreach ($v_fields['config_where_or'] as $v_field) {
                if (empty($whereor)){
                    $whereor = "(gcn.{$v_table}.{$v_field[0]} = {$v_field[1]}";
                } else {
                    $whereor.= " OR gcn.{$v_table}.{$v_field[0]} = {$v_field[1]}";
                }

            }
            $whereor .= ")";
            if (empty($where)){
                $where = $whereor;
            } else {
                $where.= " AND ".$whereor;
            }
        }


            foreach ($v_fields as $key => $v_field){
            if(substr($key, 0, 7) === "config_"){
                continue;
            }
               switch ($v_field[0]) {
                   case 0:
                      $values[] = $v_table.".".$v_field[1];
                       break;
                   case 2:
                       foreach ($v_field[1] as $v_fieldin) {
                           $values[] = $v_table . "." . $v_fieldin;
                       }
                       break;
                   case 3:
                       $tmp_values = get_fields($v_field[1],$v_table)	;
                       if(!empty($tmp_values)) {
                           $values = array_merge($values,$tmp_values);
                       }
                       break;
                   case 4:
                       $tmp_values = get_fields($v_field[1],$v_table)	;
                       if(!empty($tmp_values)) {
                           $values = array_merge($values,$tmp_values);
                       }
                       break;
                   case 5:
                       $values[] = $v_table.".".$v_field[2];
                       break;
               }

           }
    }
    $select = implode(array_unique($values),",");

    return "SELECT {$select} FROM {$from}	WHERE {$where};";

}

function workflow($arr_type,$row_res,$object,$document,$lists) {
    foreach ($arr_type as $valu){
        foreach ($valu as $key => $value) {
            if(substr($key, 0, 7) === "config_"){
                continue;
            }
            if ($value[1] == "is_part") {
                $object->appendChild($document->createElement("Category",$row_res['is_part'] ? "flatShareSale" : "flatSale"));
                continue;
            }
            switch ($value[0]){
                case 0:
                    $object->appendChild($document->createElement($key,conv_cian($row_res[$value[1]], $key)));
                    break;
                case 1:
                    $object->appendChild($document->createElement($key,$value[1]));
                    break;
                case 2:
                    $object->appendChild($document->createElement($key,conv_complex($row_res,$value[1])));
                    break;
                case 3:
                    if(!empty($value[2])) {
//                        var_dump($value[2]);
                        if(!in_array($row_res[$value[2][0]], $value[2][1]) ){
                            continue;
                        }
                    }
                    set_fields($key,$value,$row_res,$object,$document,$lists);
                     break;
                case 4:
                    $arr_res = array();
                    foreach($value[1] as $or1 => $values){
                        $ord = 0;
                        foreach ($values[key($values)] as $kv => $vals) {
                            switch ($vals[0]) {
                                case 0:
                                    if(!empty($row_res[$vals[1]])) {
                                        $arr_res[$key][$or1]['data'][$ord++] = array($kv , conv_cian($row_res[$vals[1]],$kv), key($values));
                                    }
                                    break;
                                case 1:
                                    $arr_res[$key][$or1]['const'][$ord++] = array($kv , $vals[1], key($values));
                                    break;
                            }
                        }
                    }

                    $par = true;
//					if($pari) var_dump($arr_res[$key][0]);
//					$pari = false; continue;

                    foreach ($arr_res[$key] as $values){
                        $midpar = true;
                        if(empty($values['data'])){
                            continue;
                        } else {
                            $ctrl = true; $ord=0;
                            while($ctrl){
                                $run = "";
                                if(empty($values['data'][$ord])){
                                    if(empty($values['const'][$ord])){
                                        $ctrl = false;
                                    } else {
                                        $run = "const";
                                    }

                                } else {
                                    $run = "data";
                                }
                                if(strlen($run)>0){
                                    if($par) {
                                        $parent = $object->appendChild($document->createElement($key));
                                        $par = false;
                                    }
                                    if($midpar) {
                                        $midparent = $parent->appendChild($document->createElement($values[$run][$ord][2]));
                                        $midpar = false;
                                    }
                                    $midparent->appendChild($document->createElement($values[$run][$ord][0], $values[$run][$ord][1]));
                                }
                                $ord++;
                            }
                        }
                    }

                    break;
                case 5:
                    if(!empty($row_res[$value[2]])){
//                        echo "5:".$row_res[$value[2]]." ".$value[1]."\n";
                        if(!empty($lists[$value[1]][$row_res[$value[2]]])){
                            $object->appendChild($document->createElement($key,$lists[$value[1]][$row_res[$value[2]]]));
                        }
                    }
                    break;

            }
        }
     }
}
?>
