<?php

namespace Enter\Curl;

abstract class Query implements \JsonSerializable {
    /** @var string|null */
    protected $id;
    /** @var string|null */
    protected $url;
    /**
     * Массив параметров для POST-запроса
     * @var array
     */
    protected $data = [];
    /**
     * Строка вида user:password
     * @var string
     */
    protected $auth;
    /** @var resource[] */
    protected $connections = [];
    /** @var \Exception|null */
    protected $error;
    /**
     * Таймаут, мс
     * @var int|null
     */
    protected $timeout;
    /**
     * @var int
     */
    protected $retry = 0;
    /**
     * Счетчик вызовов
     * @var int
     */
    protected $call = 0;
    /**
     * Декодированный response
     *
     * @var mixed
     */
    protected $result;
    /** @var string */
    protected $response;
    /** @var array */
    protected $headers = [];
    /** @var array */
    protected $info = [];
    /** @var float */
    protected $startAt;
    /** @var float */
    protected $endAt;

    /**
     * @return array
     */
    public function jsonSerialize() {
        $return = [
            //'id'      => $this->id,
            'url'     => (string)$this->url,
            'data'    => $this->data,
            'timeout' => $this->timeout,
            'call'    => $this->call,
            'startAt' => $this->startAt,
            'endAt'   => $this->endAt,
            'info'    => $this->info,
            'header'  => $this->headers,
        ];
        if ($this->error instanceof \Exception) {
            $return['error'] = ['code' => $this->error->getCode(), 'message' => $this->error->getMessage()];
        }
        if ($this->response) {
            $return['response'] = $this->response;
        }

        return $return;
    }

    /**
     * @param $response
     * @return void
     */
    public function callback($response) {}

    /**
     * @return mixed
     * @throws \Exception|null
     */
    final public function getResult() {
        if (0 === $this->call) {
            $this->error = new \Exception(sprintf('Запрос не подготовлен %s', $this->url));
        }
        if ($this->error) {
            throw $this->error;
        }

        return $this->result;
    }

    /**
     * @return $this
     */
    public function incCall() {
        $this->call += 1;

        return $this;
    }

    /**
     * @return int
     */
    public function getCall() {
        return $this->call;
    }

    /**
     * @param null|string $id
     * @return $this
     */
    public function setId($id) {
        $this->id = $id ? (string)$id : null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data) {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param \Exception|null $error
     * @return $this
     */
    public function setError(\Exception $error) {
        $this->error = $error;

        return $this;
    }

    /**
     * @return \Exception|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param resource $connection
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function addConnection($connection) {
        if (!is_resource($connection)) {
            throw new \InvalidArgumentException('Передан невалидный ресурс');
        }
        $this->connections[(string)$connection] = $connection;

        return $this;
    }

    /**
     * @return resource[]
     */
    public function getConnections() {
        return $this->connections;
    }

    /**
     * @param int $retry
     * @return $this
     */
    public function setRetry($retry) {
        $this->retry = (int)$retry;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetry() {
        return $this->retry;
    }

    /**
     * @param int|null $timeout
     * @return $this
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout ? (int)$timeout : null;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url ? (string)$url : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $auth
     */
    public function setAuth($auth) {
        $this->auth = $auth ? (string)$auth : null;
    }

    /**
     * @return string
     */
    public function getAuth() {
        return $this->auth;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param array $info
     */
    public function setInfo($info) {
        $this->info = (array)$info;
    }

    /**
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * @param float $endAt
     */
    public function setEndAt($endAt) {
        $this->endAt = (float)$endAt;
    }

    /**
     * @return float
     */
    public function getEndAt() {
        return $this->endAt;
    }

    /**
     * @param float $startAt
     */
    public function setStartAt($startAt) {
        $this->startAt = (float)$startAt;
    }

    /**
     * @return float
     */
    public function getStartAt() {
        return $this->startAt;
    }
}