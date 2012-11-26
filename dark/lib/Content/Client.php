<?php

namespace Content;

class Client {
    /** метод запроса: GET */
    const methodGet = 'GET';
    /** метод запроса: POST */
    const methodPost = 'POST';

    /** @var string HTTP адрес сервиса контента */
    private $url;

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return array Собирает список опций для создания стрима к сервису
     */
    private function buildOptionList($method, $timeout) {
        $options = array(
            'http' => array(
                'method'  => $method,
                'timeout' => $timeout,
                'header'  => "Content-Type: text/xml\r\n",
            )
        );

        if ($method == self::methodPost) {
            $options['http']['header'] = 'Content-type: application/x-www-form-urlencoded';
        }

        return $options;
    }

    public function send($action, $params = array(), $method = self::methodGet, $timeout = 1, $json = true) {
        \Debug\Timer::start('content');
        \App::logger()->info('Start content request ' . $action);

        if ($json) {
            $params['json'] = true;
        }

        $start = microtime(true);
        $url = $this->url.$action.'?'.http_build_query($params);
        $response = file_get_contents($url, false, stream_context_create($this->buildOptionList(
            $method,
            $timeout
        )));
        \Util\RequestLogger::getInstance()->addLog($url, array(), (microtime(true) - $start));

        if ($json) {
            $response = json_decode($response, true);

            if (isset($response['result']))
            {
                $response = $response['result'];
            }
        }

        $spend = \Debug\Timer::stop('content');
        \App::logger()->info('End content request ' . $action . ' ' . $spend);

        return $response;
    }
}