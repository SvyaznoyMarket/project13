<?php

class CoreClient
{
  /* @var array */
  private $parameters = null;

  /* @var resource */
  private $connection = null;

  /* @var sfFileLogger */
  private $logger = null;

  /* @var array */
  private $errors = array();

  /* @var CoreClient */
  protected static $instance = null;

  /**
   * @return CoreClient
   */
  static public function getInstance()
  {
    if (null == self::$instance)
    {
      self::$instance = new CoreClient();
      self::$instance->initialize(sfConfig::get('app_core_config'));
    }

    return self::$instance;
  }

  protected function initialize(array $parameters)
  {
    $this->parameters = new sfParameterHolder();
    $this->parameters->add($parameters);

    $this->connection = curl_init();
    curl_setopt($this->connection, CURLOPT_URL, $this->getParameter('userapi_url'));
    curl_setopt($this->connection, CURLOPT_HEADER, 0);
    curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getParameter('log_file')));
  }

  public function query($action, array $params = array(), array $data = array())
  {
    $isPostMethod = !empty($data);

    $query = $this->getParameter('userapi_url')
      .str_replace('.', '/', $action)
      .'?'.http_build_query(array_merge($params, array('client_id' => $this->getParameter('client_id'))));
    $this->log(($isPostMethod ? 'post' : 'get').': '.$query);

    curl_setopt($this->connection, CURLOPT_URL, $query);

    if ($isPostMethod)
    {
      curl_setopt($this->connection, CURLOPT_POST, true);
      curl_setopt($this->connection, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($this->connection);
    if (curl_errno($this->connection) > 0)
    {
      $this->errors[] = array(curl_errno($this->connection) => curl_error($this->connection));
      $response = null;
      $this->log(json_encode(end($this->errors)), 'ERROR');
    }
    if (is_array($response) && array_key_exists('error', $response))
    {
      $this->errors[] = $response['error'];
      $response = null;
      $this->log(json_encode(end($this->errors)), 'ERROR');
    }

    if ($response && $this->getParameter('log_response'))
    {
      $this->log('response: '.$response);
    }

    return json_decode($response, true);
  }

  public function getParameter($name)
  {
    return $this->parameters->get($name);
  }

  public function getParameters()
  {
    return $this->parameters->getAll();
  }

  public function log($message, $type = 'INFO')
  {
    if (!$this->getParameter('log_enabled'))
    {
      return false;
    }

    $message = preg_replace_callback(
      '/\\\u([0-9a-fA-F]{4})/',
      create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
      $message
    );

    call_user_func_array(array($this->logger, 'INFO' == $type ? 'log' : 'err'), array($message));
  }
}
