<?php
/**
 * Класс реализующий запросы к сервису контента
 *
 * @TODO: Добавить поддержку пользовательских заголовков
 */
class WPRequest
{
    /**
     * Метод запроса: GET
     */
    const methodGet = 'GET';
    /**
     * Метод запроса: POST
     */
    const methodPost = 'POST';

    /**
     * @var string HTTP адрес сервиса контента
     */
    private $url;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array Собирает список опций для создания стрима к сервису
     */
    private function buildOptionList($method, $parameterList, $timeout)
    {
        $optionList = array(
            'http' => array(
                'method' => $method,
                'content' => http_build_query($parameterList, '', '&'),
                'timeout' => $timeout

            )
        );

        if($method == self::methodPost)
        {
            $optionList['header'] = 'Content-type: application/x-www-form-urlencoded';
        }

        return $optionList;
    }

    public function send($actionUri, $parameterList = array(), $method = self::methodPost, $timeout = 1, $json = True)
    {
        if($json)
        {
            $parameterList['json'] = True;
        }

        $response = file_get_contents($this->url . $actionUri, false, stream_context_create(
            $this->buildOptionList(
                $method,
                $parameterList,
                $timeout
            )
        ));

        return $json?json_decode($response, $assoc = True):$response;
    }
}