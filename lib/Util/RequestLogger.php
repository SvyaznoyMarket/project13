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
     * @param float $startAt
     * @param float $endAt
     */
    public function addLog($url, $postData, $time, $host, $startAt = null, $endAt = null) {
        $this->request[] = [
            'host'  => $host,
            'url'   => str_replace(["\r", "\n"], '', $url),
            'post'  => $postData,
            'time'  => $time,
            'start' => $startAt,
            'end'   => $endAt,
        ];
    }

    public function getStatistics() {
        $request = \App::request();

        $data = [
            'request_id'  => $this->getId(),
            'request_uri' => $request->getRequestUri(),
            'user_agent'  => $request->server->get('HTTP_USER_AGENT'),
            'ip'          => $request->getClientIp(),
            'total_time'  => (microtime(true) - $this->startTime),
            'type'        => 'dark',
            'api_queries' => $this->request,
        ];

        return $data;
    }
}