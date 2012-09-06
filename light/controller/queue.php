<?php

namespace light;
use Logger;

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');

class queueController
{
  const WORKER_LIMIT = 10;

  /** @var \PDO */
  private $dbh;

  /** @var \Logger */
  private $logger;

  public function execute($queueName, $limit = 1000) {
    TimeDebug::start('controller:queue:execute');
    $this->logger = \Logger::getLogger('Smartengine');
    \LoggerNDC::push('batch process');

    $this->touchWorkerNum(1);

    $limit = abs($limit);

    if (!$queueName) {
      throw new \LogicException('Не указано имя задачи.');
    }

    $this->dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', DB_NAME, DB_HOST), DB_USERNAME, DB_PASSWORD);

    $this->dbh->beginTransaction();

    $clause = 'is_locked = 0 AND name'.(false === strpos($queueName, ',') ? " = '{$queueName}'" : " IN ($queueName)");
    $sth = $this->dbh->query("SELECT id, name, body FROM `queue` WHERE {$clause} LIMIT {$limit}");
    $sth->execute();

    $ids = array(); // идентификаторы заданий
    $calls = array(); // вызовы обработчиков
    while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
      $ids[] = $row['id'];
      if (!isset($calls[$row['name']])) {
        $calls[$row['name']] = array();
      }
      $calls[$row['name']][$row['id']] = json_decode($row['body'], true);
    }

    if ($ids) {
      $this->dbh->exec("UPDATE `queue` SET is_locked = 1 WHERE id IN (".implode(',', $ids).")");
    }
    $this->dbh->commit();

    foreach ($calls as $name => $data) {
      switch ($name) {
        case 'smartengine.view':
          $this->processSmartengineView($data);
          break;
      }
    }

    $this->touchWorkerNum(-1);

    TimeDebug::end('controller:queue:execute');
    \LoggerNDC::pop();
  }

  private function processSmartengineView($data) {
    require_once ROOT_PATH.'lib/smartengine/SmartengineClient.php';
    $client = new SmartengineClient(array(
      'api_url'  => SMARTENGINE_API_URL,
      'api_key'  => SMARTENGINE_API_KEY,
      'tenantid' => SMARTENGINE_TENANTID,
    ));

    $productIds = array();
    $userIds = array();
    foreach ($data as $item) {
      $productIds[$item['product_id']] = null;
      $userIds[$item['user_id']] = null;
    }
    $productIds = array_keys($productIds);
    $userIds = array_keys($userIds);

    if (!(bool)$productIds) return;

    /** @var $productsById \light\ProductData[] */
    $productsById = array();
    foreach (App::getProduct()->getProductsByIdList($productIds) as $product) {
      $productsById[$product->getId()] = $product;
    }

    try {
      foreach ($data as $item) {
        $product = isset($productsById[$item['product_id']]) ? $productsById[$item['product_id']] : null;
        if (!$product) continue;

        $params = array(
          'sessionid'       => $item['sessionid'],
          'itemid'          => $product->getId(),
          'itemdescription' => $product->getName(),
          'itemurl'         => 'http://'.$item['host'].$product->getLink(),
          'actiontime'      => $item['time'],
        );
        if ($item['user_id']) {
          $params['userid'] = $item['user_id'];
        }
        if ($product->getMainCategory()) {
          $params['itemtype'] = $product->getMainCategory()->getId();
        }

        $r = $client->query('view', $params);
        //print_r($r);
        if (isset($r['error'])) $this->logger->error('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);
      }
    } catch(\Exception $e) {
      $this->logger->error($e->getMessage());
    }

    $ids = array_keys($data);
    if ($ids) {
      $this->dbh->exec("DELETE FROM `queue` WHERE id IN (".implode(',', $ids).")");
    }
  }

  private function touchWorkerNum($num) {
    // проверка на количество одновременно запущенных воркеров
    $file = (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid';
    if (!file_exists($file)) {
      file_put_contents($file, '0');
    }
    $workerNum = (int)file_get_contents($file) + $num;
    if ($workerNum > self::WORKER_LIMIT) {
      throw new \Exception('Превышен лимит запущенных воркеров.');
    }
    file_put_contents($file, $workerNum);
  }
}
