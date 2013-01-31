<?php

namespace Controller\Queue;

// TODO: Переделать с использованием QueueManager
class Action {

    /** @var \PDO */
    private $dbh;
    /** @var \Logger\LoggerInterface */
    private $logger;

    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }

    public function execute($queueName, $limit = 1000) {
        echo "Executing '{$queueName}' with: limit={$limit} ...\n";
        \App::logger()->debug('Exec ' . __METHOD__);

        $this->logger = \App::logger('smartengine');

        $this->touchWorkerNum(1);

        try {
            $limit = abs($limit);

            if (!$queueName) {
                throw new \LogicException('Не указано имя задачи.');
            }

            $this->dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', \App::config()->database['name'], \App::config()->database['host']), \App::config()->database['user'], \App::config()->database['password'], array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ));

            $this->dbh->beginTransaction();

            // (незаблокированные или вылетившие по таймауту) и с именем {$queueName}
            $clause = '(locked_at IS NULL OR TIMESTAMPDIFF(SECOND, locked_at, NOW()) > '.\App::config()->queue['maxLockTime'].') AND name'.(false === strpos($queueName, ',') ? " = '{$queueName}'" : " IN ($queueName)");
            $sth = $this->dbh->query("SELECT id, name, body FROM `queue` WHERE {$clause} LIMIT {$limit}");
            $sth->execute();

            $ids = []; // идентификаторы заданий
            $calls = []; // вызовы обработчиков
            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $ids[] = $row['id'];
                if (!isset($calls[$row['name']])) {
                    $calls[$row['name']] = [];
                }
                $calls[$row['name']][$row['id']] = json_decode($row['body'], true);
            }

            if ($ids) {
                $this->dbh->exec("UPDATE `queue` SET locked_at = NOW() WHERE id IN (".implode(',', $ids).")");
            }
            $this->dbh->commit();

            foreach ($calls as $name => $data) {
                switch ($name) {
                    case 'smartengine.view':
                        $this->processSmartengineView($data);
                        break;
                    case 'smartengine.buy':
                        $this->processSmartengineBuy($data);
                        break;
                }
            }
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
        }

        $this->touchWorkerNum(-1);
    }

    private function processSmartengineView($data) {
        echo "Process smartengine.view with: ".count($data)." items ...\n";

        $client = \App::smartengineClient();

        $productIds = [];
        foreach ($data as $item) {
            $productIds[$item['product_id']] = null;
        }
        $productIds = array_keys($productIds);

        if (!(bool)$productIds) return;

        $region = \RepositoryManager::region()->getDefaultEntity();
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        foreach (\RepositoryManager::product()->getCollectionById($productIds, $region) as $product) {
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
                if (!empty($item['user_id'])) {
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
            echo "Error {$e->getMessage()} ...\n";
        }

        $ids = array_keys($data);
        if ($ids) {
            $this->dbh->exec("DELETE FROM `queue` WHERE id IN (".implode(',', $ids).")");
        }
    }

    private function processSmartengineBuy($data) {
        echo "Process smartengine.buy with: ".count($data)." items ...\n";

        $client = \App::smartengineClient();

        $productIds = [];
        foreach ($data as $item) {
            foreach ($item['order']['product'] as $product) {
                $productIds[] = $product['id'];
            }
        }
        if (!(bool)$productIds) return;

        $region = \RepositoryManager::region()->getDefaultEntity();
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        foreach (\RepositoryManager::product()->getCollectionById($productIds, $region) as $product) {
            $productsById[$product->getId()] = $product;
        }

        try {
            foreach ($data as $item) {
                foreach ($item['order']['product'] as $product) {

                    $params = array(
                        'sessionid'       => $item['sessionid'],
                        'itemid'          => $product['id'],
                        'itemdescription' => $productsById[$product['id']]->getName(),
                        'itemurl'         => 'http://'.$item['host'].$productsById[$product['id']]->getLink(),
                        'actiontime'      => $item['time'],
                        'orderid'         => $item['order']['id'],
                        'unitprice'       => $product['price'],
                        'quantity'        => $product['quantity'],
                    );
                    if ($item['user_id']) {
                        $params['userid'] = $item['user_id'];
                    }
                    if ($productsById[$product['id']]->getMainCategory()) {
                        $params['itemtype'] = $productsById[$product['id']]->getMainCategory()->getId();
                    }

                    $r = $client->query('buy', $params);
                    //print_r($r);
                    if (isset($r['error'])) $this->logger->error('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);
                }
            }
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            echo "Error {$e->getMessage()} ...\n";
        }

        $ids = array_keys($data);
        if ($ids) {
            $this->dbh->exec("DELETE FROM `queue` WHERE id IN (".implode(',', $ids).")");
        }
    }

    private function touchWorkerNum($num) {
        // проверка на количество одновременно запущенных воркеров
        $file = \App::config()->queue['pidFile'];

        $fp = fopen($file, 'c+');
        while (!$fp) {
            $pause = rand(100000, 3000000);
            echo "\nКажется, файл заблокирован. Жду $pause ".($pause / 1000)."ms...";
            usleep($pause);
            $fp = fopen($file, 'c+');
        }

        if ($fp) {
            $count = (int)file_get_contents($file);
            if (($num > 0) && ($count > \App::config()->queue['workerLimit'])) {
                throw new \Exception('Превышен лимит запущенных воркеров.');
            }

            $count = $count + $num;

            file_put_contents($file, $count >= 0 ? $count: 0);
            fclose($fp);
        }
        else {
            echo "Не удалось открыть файл.\n";
        }
    }

}