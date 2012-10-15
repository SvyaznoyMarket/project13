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
    private function buildOptionList($method, $params, $timeout) {
        $optionList = array(
            'http' => array(
                'method'  => $method,
                'content' => http_build_query($params, '', '&'),
                'timeout' => $timeout

            )
        );

        if ($method == self::methodPost) {
            $optionList['header'] = 'Content-type: application/x-www-form-urlencoded';
        }

        return $optionList;
    }

    public function send($action, $params = array(), $method = self::methodPost, $timeout = 30, $json = true) {
        \Debug\Timer::start('content');
        \App::logger()->info('Start content request ' . $action);

        if ($json) {
            $params['json'] = true;
        }

        $response = file_get_contents($this->url . $action, false, stream_context_create(
            $this->buildOptionList($method, $params, $timeout)
        ));

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