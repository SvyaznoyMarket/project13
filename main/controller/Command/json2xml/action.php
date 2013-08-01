<?

// for debug
@error_reporting(E_ALL);
@ini_set('display_errors', TRUE);


include_once "json2xmlClass.php";

$json2xml = new \Controller\Command\json2xml;

$json2xml->execute();

$json2xml = null;


?>