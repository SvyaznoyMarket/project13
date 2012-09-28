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
    private function buildOptionList($method, $timeout)
    {
        $optionList = array(
            'http' => array(
                'method' => $method,
                'timeout' => $timeout

            )
        );

        if($method == self::methodPost)
        {
            $optionList['http']['header'] = "Content-Type: application/x-www-form-urlencoded\r\n";
        }

        return $optionList;
    }

    public function send($actionUri, $parameterList = array(), $method = self::methodGet, $timeout = 1, $json = True)
    {
        if($json)
        {
            $parameterList['json'] = True;
        }

      $params = $this->buildOptionList(
        $method,
        $timeout
      );

      sfContext::getInstance()->getLogger()->info('Trying to get ['.$this->url . $actionUri.'] with params ['.print_r($params, true).']');

      $response = file_get_contents($this->url.$actionUri.'?'.http_build_query($parameterList), false, stream_context_create($params));

        if($json)
        {
            $response = json_decode($response, $assoc = True);

            if (!$response)
            {
              sfContext::getInstance()->getLogger()->err('Bad response');
              sfContext::getInstance()->getLogger()->err($response);
            }

          if(isset($response['result']))
            {
                $response = $response['result'];
            }
        }

        return $response;
    }
}
