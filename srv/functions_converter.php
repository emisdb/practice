<?php
/**
 * Основные Функции конвертора
 *
 * @package functions_convertor
 */

/**
 * Автозагрузка
 */
require_once('main/autoload.php');

$emls_prefix = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => 'A', '11' => 'B', '12' => 'C', '13' => 'D', '14' => 'E', '15' => 'F', '16' => 'G', '17' => 'H', '18' => 'I', '19' => 'J', '20' => 'K', '21' => 'L', '22' => 'M', '23' => 'N', '24' => 'O', '25' => 'P');

/**
 * Связывает идентификаторы агента и его обьекты недвижимости и выводит в файл
 *
 * @param $handle integer указатель на файл
 * @param $nameTabObjects string имя таблицы объектов которые необходимо связать
 * @param $gcn_connetion integer открытое mysql подключение к базе gcn
 * @param $tbs string символ разделитель
 * @param $arrKris array Крис массив
 *
 * @return string строка для Крис
 */
function getLinkedAgentWithObject($handle, $nameTabObjects, $gcn_connetion, $tbs, $arrKris)
{

    static $firstIteration = true;

    //Получим список объектов в массив
    $query = "SELECT id ,id_user FROM $nameTabObjects
                WHERE objects_status = 0";

    if ($nameTabObjects == "gcn_comm") // what is this fack ? -> gcn_comm ... only_for_gcn
        $query .= " and only_for_gcn = 0";

    if ($result = $gcn_connetion->query($query)) {
        //начнем выгрузку в файл
        while ($rowObjs = $result->fetch_assoc()) {
            $query_ = 'SELECT LocalTN FROM gcn_kadr
                      WHERE id_agent = ' . $rowObjs['id_user'];

            if ($resultLocalTNs = $gcn_connetion->query($query_)) {

                if ($resultLocalTNs->lengths > 0) {
                    $row = $resultLocalTNs->fetch_assoc();
                    $id_agent = $row["LocalTN"];
                } else
                    $id_agent = $rowObjs['id_user'];

                //if ($firstIteration == true)
                extFwrite($handle, $rowObjs['id'], $arrKris, ord($tbs));
                //else
                //  extFwrite($handle, $rowObjs['id']);

                extFwrite($handle, $tbs);
                extFwrite($handle, $id_agent);
                extFwrite($handle, $tbs);
                extFwrite($handle, "\r\n");

                $firstIteration = false;
            }
            mysqli_free_result($resultLocalTNs);
        }
    }
    mysqli_free_result($result);
}

/**
 * Расширенная функция для валидации и проверки через представление - формируется таблица (занчение + номер строки)
 * Более подробно см. - EmlsDebug::extFwrite
 *
 * @param $handle string указатель на файл куда пишутся данные или const ENDFILE - конец записи
 * @param $string string значение(поле) которое небходимо записать
 * @param $array_table_field array Крис массив
 * @param $tbs_ string символ разделитель
 * @param $onView integer опция выбора VIEW_TABLE или VIEW_LINES
 *
 * @return void
 */
function extFwrite($handle, $string, $array_table_field, $tbs_, $onView = EmlsDebug::VIEW_TABLE)
{
    // DEBUG_OFF

    if ($onView == EmlsDebug::VIEW_LINES
        || $onView == EmlsDebug::VIEW_TABLE
        || $handle == EmlsDebug::ENDFILE
    ) {
        // получение номера строки места из кторого просиходит вызов
        $debug = debug_backtrace();
        $numLine = $debug[0]['line'];
        
        EmlsDebug::extFwrite($handle, $string, $array_table_field, $tbs_, $onView, $numLine);
    }

    //if (mb_detect_encoding($str) != 'windows-1251')
    //  $string = iconv(mb_detect_encoding($string), 'windows-1251', $string);

    if ($handle != EmlsDebug::ENDFILE)
        fwrite($handle, $string);
}

/**
 * Открытие и запись в файл Крис структуры(названий полей)
 *
 * @param $kris array структура полей Крисс базы
 * @param $argv array название argv[1] файла куда происходит запись
 * @param $tbs integer код символа - разделителя
 *
 * @return integer указатель на открытый файл
 */
function writeKrisTypes($kris, $argv, $tbs)
{

    if (is_null($argv[1]))
        $argv[1] = 'out/test.mdb';
    //создадим файл для записи туда наших объектов недвижимости
    $handle = fopen($argv[1], "w+");

    $countKrisFields = count($kris);

    //Запишем в файл столбцы-названия полей
    for ($i = 1; $i <= $countKrisFields; $i++) {

        if (mb_detect_encoding($kris[$i]) != 'windows-1251')
            $kris[$i] = iconv(mb_detect_encoding($kris[$i]), 'windows-1251', $kris[$i]);

        fwrite($handle, $kris[$i]);

        //Если последнее значение в цикле, то последний разделитель писать не надо
        if ($i < $countKrisFields)
            fwrite($handle, chr($tbs));
    }
    fwrite($handle, "\r\n");

    return $handle;
}

/**
 * Переоткрытие файла для добавления записей
 *
 * @param $argv array ередаваемы парамерты при вызове скрипта
 *
 * @return integer указатель на переоткрытый файл
 */
function reopenFile($argv)
{

    if (is_null($argv[1]))
        $argv[1] = 'out/test.mdb';

    return fopen($argv[1], "a");
}


/**
 * Конвертор строки от mysql, полей формата windows-1251 в utf-8
 * Используется при fetch переборе строк
 *
 * @param $row array массив полей (in, out)
 *
 * @return void
 */
function incovRow(&$row)
{

    foreach ($row as $key => $col) {
        if (mb_detect_encoding($col, 'UTF-8', true) === false) {
            $row[$key] = iconv('windows-1251', 'utf-8', $col);
        }
    }
}

const START = 1;
const END = 2;
/**
 * Отображает информацию при старте и завершении работы скрипта
 *
 * @param $type integer вывод инфы в начале загрузки, и в конце
 * @param $outFile string файл в который происходит выгрузка
 *
 * @return void
 */
function infoStartEndSh($type, $outFile)
{

    if ($type == START)
        return 'Старт ' . $_SERVER['PHP_SELF'] . ' ' . date('Y-d-m h:i:s') . ': ';
    else if ($type == END)
        return "файл - $outFile создан \n";
}

/**
 * Запуск скрипта по шелл
 *
 * @param $nameShellScript string запускаемый скрипт
 * @param $outFile string файл в который происходит выгрузка
 * @param $log string лог файл
 * @param $emls_path string папка выгрузки
 *
 * @return void
 */
function run($nameShellScript, $outFile, &$log, $emls_path)
{
    /*
    putenv('LANG=en_US.UTF-8');
    shell_exec('export LANG="ru_RU.UTF-8"');
    shell_exec('export LC_ALL="ru_RU.UTF-8"');
    */
    $dir = dirname(__FILE__);

    $log .= infoStartEndSh(START, null);
    $log .= shell_exec('php ' . $nameShellScript . ' ' . $dir . $emls_path . $outFile) . "\n";
    $log .= infoStartEndSh(END, $outFile);
}

/**
 * Возвращает дату в формате ЕМЛС а именно ДД.ММ.ГГГГ
 *
 * @param $date_in date дата
 * @param $date_in integer тип даты
 *  0 дата в формате unix,
 *  1 - дата в формате ГГГГ-ММ-ДД,
 *  3 - дата начала и окончания контракта для коммерческой недвижимости.
 *
 * @return mixed
 */
function make_emls_date($date_in, $date_type, $date_end)
{
    if (is_null($date_in)) {
        if (is_null($date_end))
          return "";
        $date_elements = explode("-", $date_end);
        return $date_elements[2] . "." . $date_elements[1] . "." . $date_elements[0];
    }
    
    if ($date_type == 1) {
        $date_elements = explode("-", $date_in);
        return $date_elements[2] . "." . $date_elements[1] . "." . $date_elements[0];
    }

    if ($date_type == 3) {
        if ($date_in == "01.01.0001")
            return "";
        else
            return $date_in;
    }        
} 

/**
 * Проверка на null или пусто с заменой на 1
 *
 * @param $int_in integer
 * @return mixed
 */
function if_null($int_in)
{
    if ($int_in == "")
        return 0;
    else
        return $int_in;
}

/**
 * Проверка на null, пусто, 0 с заменой на 1 для справочников
 *
 * @param $codesprav_in integer
 * @return mixed
 */
function if_null_sprav($codesprav_in)
{
    if ($codesprav_in == "" || $codesprav_in == 0)
        return 1;
    else
        return $codesprav_in;
}

/**
 * Формирует строку фотографий для конвертера в ЕМЛС
 *
 * @param $id_obj integer объект недвижимости
 * @param $id_base integer берется из gcn_list_type_base
 * @param $gcn_connetion integer открытое mysql подключение к базе gcn
 *
 * @return string
 */
function make_foto_emls_string($id_obj, $id_base, $gcn_connetion)
{

    global $emls_prefix;
    $foto_query = "SELECT photo_file FROM gcn_foto
                  WHERE id_object = $id_obj
                    AND id_base = $id_base
                    AND photo_status = 1
                  ORDER BY photo_sorting ASC";

    $result_foto_table = $gcn_connetion->query($foto_query);
    if (mysqli_num_rows($result_foto_table) == 0)
        return "";

    //Получим первую ссылку на фотографию
    $row_foto_string = $result_foto_table->fetch_assoc();
    $x = 1;
    $foto_string = $emls_prefix[$x] . '-' . substr($row_foto_string['photo_file'], 1) . ";";

    //теперь получаем остальные ссылки на фото, если они есть
    $x = 2;
    while ($row_foto_string = $result_foto_table->fetch_assoc()) {
        $foto_string = $foto_string . $emls_prefix[$x] . '-' . substr($row_foto_string['photo_file'], 1) . ";";
        //echo$emls_prefix[$x].'-';
        $x++;
    }

    return $foto_string;
}

/**
 * Справочные значения для метро
 *
 * @param $metro_how_get_in integer идент. метро
 * @param $gcn_connetion integer открытое mysql подключение к базе gcn
 *
 * @return mixed Код способа добраться до метро, справочное,
 *  Время до ближайшей станции метро,
 *  Единицы измерения - минуты, остановки и т.п.
 */
function make_metro_how_get($metro_how_get_in, $gcn_connetion)
{

    $metro_how_get_query = "SELECT * FROM gcn_list_metro_how_get
                          WHERE id_item = $metro_how_get_in";

    $result_metro_how_get = $gcn_connetion->query($metro_how_get_query);

    //Если соответствий не найдено, возвращаем следующий массив
    if (mysqli_num_rows($result_metro_how_get) == 0)
        return array(1, "", "");

    $row_metro_how_get = $result_metro_how_get->fetch_assoc();

    mysqli_free_result($result_metro_how_get);

    return array(
        $row_metro_how_get['emls_offline_kmtype'],
        $row_metro_how_get['emls_offline_kmtime'],
        $row_metro_how_get['emls_offline_munit']
    );
}

/**
 * Преобразовывает перевод строки, каретки или символ новой строки в пробел
 *
 * @param $string_in string исходная строка
 *
 * @return string
 */
function replace_rn($string_in)
{
    $replacement = array("\r\n", "\n", "\r");
    $replace = " ";

    return str_replace($replacement, $replace, $string_in);
}

/**
 * Возвращает строку с текстом, в зависимости от идентификатора типа цены коммерческой недвижимости
 *
 * @param $id_price_in integer идентификатор цены
 * @param $gcn_connetion integer открытое mysql подключение к базе gcn
 *
 * @return string
 */
function make_kn_price($id_price_in, $gcn_connetion)
{
    $kn_price_query = "SELECT word FROM r_list_kn_prices WHERE id = $id_price_in";

    $result_kn_price_query = $gcn_connetion->query($kn_price_query);
    if ($result_kn_price_query->field_count == 0)
        return "";

    $row_result_kn_price_query = $result_kn_price_query->fetch_assoc();
    $str_price_type = $row_result_kn_price_query['word'];
    return $str_price_type;
}

/**
 * Возвращает строку с текстом, в зависимости от номера этажа, кол-ва этажей
 *
 * @param $floor integer этаж
 * @param $floor integer кол-во этажей в доме
 * @param $date_end integer дата окончания строительства
 *
 * @return string
 */
function make_nls_builds($floor, $floors, $date_end)
{
    return $floor . ';' . $floors . ';' . date('d.m.Y', $date_end);
}

/**
 * Показать комментарии для таблицы
 *
 * @param $table string название таблицы
 * @return void
 */
function getAllCommasFromTbl($table)
{
    $sql = "SELECT column_name, column_comment FROM information_schema.columns WHERE table_name = '" . $table . "';";
    $qtable = mysql_query($sql);

    while ($row = mysql_fetch_assoc($qtable)) {
        foreach ($row as $next) {
            echo "  " . iconv('windows-1251', 'utf-8', $next);
        }
        echo "\n";
    }
}
//Создаем функцию для заполнения тега (</creation-date>)
	function get_Datetime_ISO8601($date_edit) {
    $tz_object = new DateTimeZone('Europe/Moscow');
    $datetime = new DateTime($date_edit);
    $datetime->setTimezone($tz_object);
    return $datetime->format('c');
	}
?>
