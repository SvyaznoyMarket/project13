<?php
namespace Util;
use Exception;

/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 30.08.12
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class RequestLogger {
    /**
     * Инстанс для синглтона
     * @var RequestLogger
     */
    static private $instance = null;

    /**
     * Уникальный идентификатор запроса к сайту
     * @var null
     */
    private $id = null;

    private $startTime = null;

    /**
     * Список запросов к ядру
     * @var array
     */
    private $request = [];

    /**
     * Получит инстанс для синглтона
     * @static
     * @return RequestLogger
     */
    static public function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new RequestLogger();
        }

        return self::$instance;
    }

    /**
     * Получить уникальный идентификатор запроса
     * @return null
     */
    public function getId() {
        if (empty($this->id)) {
            $this->id = uniqid();
        }

        return $this->id;
    }

    /**
     * Устанавливает уникальный идентификатор запроса
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    private function __construct() {
        $this->startTime = microtime(true);
    }

    public function __clone() {
        return false;
    }

    /**
     * Добавляет запрос в список запросов от ядра
     * @param string $url
     * @param array $postData
     * @param string $time
     * @param $host
     */
    public function addLog($url, $postData, $time, $host) {
        $this->request[] = ['time' => $time, 'host' => $host, 'url' => str_replace(["\r", "\n"], '', $url), 'post' => $postData];
    }

    public function getStatistics() {
        $data = ['request_id' => $this->getId(), 'request_uri' => $_SERVER['REQUEST_URI'], 'api_queries' => [], 'total_time' => (microtime(true) - $this->startTime), 'type' => 'dark'];

        foreach ($this->request as $log) {
            $data['api_queries'][] = ['host' => $log['host'], 'url' => $log['url'], 'post' => $log['post'], 'time' => $log['time']];
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}