<?

// for debug
@error_reporting(E_ALL);
@ini_set('display_errors', TRUE);


include_once "json2xmlClass.php";


// Максимальное кол-во строчек фалйа, которое будет прочитано (проходов цикла)
define("MAX_IERATIONS", 30); // Для проверки.
//define("MAX_IERATIONS", -1); // При отрицательном значении обходит все строки файла


$config = [
    'input_json_file' => '../../../../../../cms.enter.ru/product-export.json',
    'output_xml_file' => 'market.xml',
    'log_format' => 'console', // Формат вывода, для консоли, например, теги <pre> не выводятся
    //'log_format' => 'web',
    'log_file_name' => 'json2xml_log.txt',
    'coreUrl' => 'http://tester.core.ent3.ru/v2/',
];


$params = []; // пока нету


// Go, go, go!
(new \Controller\Command\json2xml($config) )->execute( $params );

?>