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
     * @var string URL для обращения к сервису
     */
    private $url;
    /**
     * @var array Список параметров отправляемых сервису
     */
    private $parameterList = array();
    /**
     * @var string Текущий метод запроса
     */
    private $method = self::methodGet;
    /**
     * @var int Время ожидания ответа сервиса
     */
    private $timeout = 30;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getParameterList()
    {
        return $this->parameterList;
    }

    public function setParameterList(array $parameterList)
    {
        $this->parameterList = $parameterList;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        if(!in_array($method, array(self::methodGet, self::methodPost)))
        {
            throw new InvalidArgumentException('Invalid request method specified');
        }

        $this->method = $method;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return array Собирает список опций для создания стрима к сервису
     */
    private function buildOptionList()
    {
        $optionList = array(
            'http' => array(
                'method' => $this->method,
                'content' => http_build_query($this->parameterList, '', '&'),
                'timeout' => $this->timeout

            )
        );

        if($this->method == self::methodPost)
        {
            $optionList['header'] = 'Content-type: application/x-www-form-urlencoded';
        }

        return $optionList;
    }

    /**
     * Отправляет запрос сервису
     *
     * @param $actionUri string Запрашиваемое действие сервиса
     * @return array Ответ сервиса
     */
    public function send($actionUri)
    {
        $response = file_get_contents($this->url . $actionUri, false, stream_context_create($this->buildOptionList()));

        return json_decode($response, $assoc = True);
    }
}