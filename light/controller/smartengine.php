<?php
namespace light;
use Logger;

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

class smartengineController
{
  private $dbh;

  public function view(Response $response, $params = array()){
    if (!$productId = (int)$_GET['productId']) return;

    try {
      $data = array(
        'host'       => App::getRequest()->getHost(),
        'time'       => date('d_m_Y_H_i_s'),
        'sessionid'  => session_id(),
        'product_id' => (int)$productId,
        'user_id'    => App::getCurrentUser()->isAuthorized() ? App::getCurrentUser()->getUser()->getId() : null,
      );

      $this->getDbh()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.view', '".json_encode($data)."')");
    }
    catch (\Exception $e) {
    }
  }

  public function buy(Response $response, $params = array()){
    $ids = explode('-', $_GET['product']);
    if (!$ids) return;

    try {
      $data = array(
        'host'        => App::getRequest()->getHost(),
        'time'        => date('d_m_Y_H_i_s'),
        'sessionid'   => session_id(),
        'product_ids' => $ids,
        'user_id'     => App::getCurrentUser()->isAuthorized() ? App::getCurrentUser()->getUser()->getId() : null,
      );

      $this->getDbh()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.buy', '".json_encode($data)."')");
    }
    catch (\Exception $e) {
    }
  }

  private function getDbh() {
    if (!$this->dbh) {
      $this->dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', Config::get('db.name'), Config::get('db.host')), Config::get('db.user'), Config::get('db.password'), array(
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      ));
    }

    return $this->dbh;
  }
}
