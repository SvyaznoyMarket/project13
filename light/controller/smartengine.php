<?php
namespace light;
use Logger;

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');

class smartengineController
{
  public function view(Response $response, $params = array()){
    if (!$productId = (int)$_GET['productId']) return;

    try {
      $dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', DB_NAME, DB_HOST), DB_USERNAME, DB_PASSWORD, array(
        //\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      ));

      $data = array(
        'host'       => App::getRequest()->getHost(),
        'time'       => date('d_m_Y_H_i_s'),
        'sessionid'  => session_id(),
        'product_id' => (int)$productId,
        'user_id'    => App::getCurrentUser()->isAuthorized() ? App::getCurrentUser()->getUser()->getId() : null,
      );

      $dbh->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.view', '".json_encode($data)."')");
    }
    catch (\Exception $e) {
    }
  }
}
