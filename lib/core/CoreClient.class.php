<?php

class CoreClientException extends Exception
{
}

class CoreClient
{
  /* @var sfParameterHolder */
  private $parameters = null;

  /* @var sfFileLogger */
  private $logger = null;

  /**
   * @return CoreClient
   */
  static public function getInstance()
  {
    static $instance;
    if (!$instance) {
      $instance = new CoreClient(sfConfig::get('app_core_config'));
    }
    return $instance;
  }

  private function __construct(array $parameters)
  {
    $this->parameters = new sfParameterHolder();
    $this->parameters->add($parameters);
    $this->logger = new sfAggregateLogger(new sfEventDispatcher());
    $this->logger->addLogger(new sfFileLogger(new sfEventDispatcher(), array('file' => $this->parameters->get('log_file'))));
    //$this->logger->addLogger(sfContext::getInstance()->getLogger());
  }

  /**
   * @param $action
   * @param array $params
   * @param array $data
   * @return array
   * @throws CoreClientException
   */
  public function query($action, array $params = array(), array $data = array())
  {
    $isPostMethod = !empty($data);

    $query = $this->parameters->get('userapi_url')
      . str_replace('.', '/', $action)
      . '?' . http_build_query(array_merge($params, array('client_id' => $this->parameters->get('client_id'))));

    if ($this->parameters->get('log_enabled')) {
      $this->logger->info('Send core requset ' . ($isPostMethod ? 'post' : 'get') . ': ' . $query);
    }

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_URL, $query);

    if ($isPostMethod) {
      curl_setopt($connection, CURLOPT_POST, true);
      curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($connection);
    try {
      if (curl_errno($connection) > 0) {
        throw new CoreClientException(curl_error($connection), curl_errno($connection));
      }
      $responseDecoded = $this->decode($response);
      if ($this->parameters->get('log_enabled')) {
        $this->logger->info('Core response data: ' . $this->encode($responseDecoded));
        $this->logger->info('Core response info: ' . $this->encode(curl_getinfo($connection)));
      }
      curl_close($connection);
      return $responseDecoded;
    }
    catch (CoreClientException $e) {
      curl_close($connection);
      $this->logger->err($e->__toString());
      throw $e;
    }
  }

  /**
   * @param $response
   * @return array
   * @throws CoreClientException
   */
  private function decode($response)
  {
    $decoded = json_decode($response, true);
    // check json error
    if ($code = json_last_error()) {
      switch ($code) {
        case JSON_ERROR_DEPTH:
          $error = 'Maximum stack depth exceeded';
          break;
        case JSON_ERROR_STATE_MISMATCH:
          $error = 'Underflow or the modes mismatch';
          break;
        case JSON_ERROR_CTRL_CHAR:
          $error = 'Unexpected control character found';
          break;
        case JSON_ERROR_SYNTAX:
          $error = 'Syntax error, malformed JSON';
          break;
        case JSON_ERROR_UTF8:
          $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
          break;
        default:
          $error = 'Unknown error';
          break;
      }
      $errorMessage = sprintf('Json error: "%s", Response: "%s"', $error, $response);
      throw new CoreClientException($errorMessage, $code);
    }

    if (is_array($decoded) && array_key_exists('error', $decoded)) {
      throw new CoreClientException(json_encode($decoded['error']));
    }
    return $decoded;
  }

  /**
   * @param $data
   * @return string
   */
  private function encode($data)
  {
    $data = json_encode($data);
    $data = preg_replace_callback(
      '/\\\u([0-9a-fA-F]{4})/',
      function($match){
        return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");
      },
      //create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
      $data
    );
    return $data;
  }
}
