<?php

class QueueManager {
    /** @var array */
    private $config;
    /** @var \Logger\LoggerInterface */
    private $logger;
    /** @var \PDO */
    private $dbh;

    public function __construct(array $config, \Logger\LoggerInterface $logger) {
        $this->config = array_merge([
            'pidFile'     => null,
            'workerLimit' => null,
            'maxLockTime' => null,
        ], $config);

        $this->logger = $logger;
        $this->dbh = \App::database();
    }

    /**
     * @param string   $queueName
     * @param callback $handler
     * @param int      $limit
     * @throws LogicException
     * @throws Exception
     */
    public function process($queueName, $handler, $limit = 1000) {
        $this->logger->debug(sprintf('Executing %s with limit %s', $queueName, $limit));

        if (!is_callable($handler)) {
            throw new \Exception(sprintf('Для задания %s передан неправильный обработчик', $queueName));
        }

        // important!
        $this->touchWorkerNum(1);

        try {
            $limit = abs($limit);

            if (!$queueName) {
                throw new \LogicException('Не указано имя задачи');
            }

            $this->dbh->beginTransaction();

            // (незаблокированные или вылетившие по таймауту) и с именем {$queueName}
            $clause = '(locked_at IS NULL OR TIMESTAMPDIFF(SECOND, locked_at, NOW()) > ' . $this->config['maxLockTime'] . ') AND name' . (false === strpos($queueName, ',') ? " = '{$queueName}'" : " IN ($queueName)");
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
                $this->dbh->exec("UPDATE `queue` SET locked_at = NOW() WHERE id IN (" . implode(',', $ids) . ")");
            }

            $this->dbh->commit();

            foreach ($calls as $name => $data) {
                try {
                    $handler($name, $data);
                } catch (\Exception $e) {
                    // TODO: добавить attempt
                    $this->logger->error($e);
                }
            }

            if ($ids) {
                $this->dbh->exec("DELETE FROM `queue` WHERE id IN (".implode(',', $ids).")");
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        // important!
        $this->touchWorkerNum(-1);
    }

    /**
     * @param int $num
     * @throws Exception
     */
    private function touchWorkerNum($num) {
        // проверка на количество одновременно запущенных воркеров
        $file = $this->config['pidFile'];

        $fp = fopen($file, 'c+');
        while (!$fp) {
            $pause = rand(100000, 3000000);
            $this->logger->warn(sprintf('Кажется, файл заблокирован. Жду %s ms...', $pause / 1000));
            usleep($pause);
            $fp = fopen($file, 'c+');
        }

        if ($fp) {
            $count = (int)file_get_contents($file);
            if (($num > 0) && ($count > $this->config['workerLimit'])) {
                throw new \Exception('Превышен лимит запущенных воркеров.');
            }

            $count = $count + $num;

            file_put_contents($file, $count >= 0 ? $count: 0);
            fclose($fp);
        }
        else {
            $this->logger->warn(sprintf('Не удалось открыть файл %s', $file));
        }
    }
}