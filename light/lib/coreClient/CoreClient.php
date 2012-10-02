<?php
namespace light;
use Logger;
use Exception;

require_once(__DIR__.'/../log4php/Logger.php');
require_once(__DIR__ . '/../helper/RequestLogger.php');

class CoreClient
{
  /* @var array */
  private $parameters = array();

  /** @var resource */
  private $multiHandler;
  /** @var callback[] */
  private $callbacks = array();
  private $resources = array();

  /**
   * @var Logger[]
   */
  private $loggers = array();

  /**
   * @return CoreClient
   */
  static public function getInstance()
  {
    static $instance;
    if (!$instance) {
      $instance = new CoreClient(array('userapi_url' => Config::get('coreV2UserAPIUrl'), 'client_code'=> Config::get('coreV2UserAPIClientCode')));
      $instance->addLogger('CoreClient', Logger::getLogger('CoreClient'));
    }
    return $instance;
  }

  private function __construct(array $parameters)
  {
    $this->parameters = $parameters;
  }

  private function __clone(){}

  public function addLogger($name, Logger $logger){
    $this->loggers[$name] = $logger;
  }

  public function removeLogger($name){
    $this->loggers[$name] = null;
    unset($this->loggers[$name]);
  }

  /**
   * Run synchronous query.
   *
   * @param $action
   * @param array $params
   * @param array $data
   * @return array
   * @throws RuntimeException
   */
  public function query($action, array $params = array(), array $data = array())
  {
    $time_start = microtime(true);
    $params['uid'] = RequestLogger::getInstance()->getId();
    $connection = $this->createCurlResource($action, $params, $data);
    $response = curl_exec($connection);
    $time_end = microtime(true);
    $this->log('Request time:' . ($time_end - $time_start), 'info');
    try {
      if (curl_errno($connection) > 0) {
        throw new \RuntimeException(curl_error($connection), curl_errno($connection));
      }
      $info = curl_getinfo($connection);
      $this->log('Core response resource: ' . $connection ,'debug');
      $this->log('Core response info: ' . $this->encodeInfo($info), 'debug');
      if ($info['http_code'] >= 300) {
        throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
      }
      $this->log('Core response: ' . $response, 'debug');
      RequestLogger::getInstance()->addLog($action, $params);
      $responseDecoded = $this->decode($response);
      curl_close($connection);
      return $responseDecoded;
    }
    catch (\RuntimeException $e) {
      curl_close($connection);
      $this->log('request params: '.$action.', get: '.print_r($params, 1).', post: '.print_r($data, 1).'error: '.$e->__toString() . ', response: ' . print_r($response, 1), 'error');
      throw $e;
    }
  }

  /**
   * Add task to queue.
   *
   * @see execute
   * @param $action
   * @param array $params
   * @param array $data
   * @param callback $callback
   */
  public function addQuery($action, array $params = array(), array $data = array(), $callback)
  {
    if (!$this->multiHandler) {
      $this->multiHandler = curl_multi_init();
    }
    $resource = $this->createCurlResource($action, $params, $data);
    curl_multi_add_handle($this->multiHandler, $resource);
    $this->callbacks[(string)$resource] = $callback;
    $this->resources[] = $resource;
  }

  /**
   * Run all added query in parallel mode.
   * Callbacks are called in the order of answers http, and not in order of call addQuery.
   * Note, application is blocked until the processing of all requests.
   *
   * @see addQuery
   * @throws RuntimeException
   */
  public function execute()
  {
    if (!$this->multiHandler)
      throw new \RuntimeException('No query to execute');

    $active = null;
    $error = null;
    try {
      $time_start = microtime(true);
      do {
        $code = curl_multi_exec($this->multiHandler, $still_executing);
        if ($code == CURLM_OK) {
          // if one or more descriptors is ready, read content and run callbacks
          while ($done = curl_multi_info_read($this->multiHandler)) {
            $this->log('Core response done: ' . print_r($done, 1), 'debug');
            $ch = $done['handle'];
            $info = curl_getinfo($ch);
            $this->log('Core response resurce: ' . $ch, 'debug');
            $this->log('Core response info: ' . $this->encodeInfo($info), 'debug');
            if (curl_errno($ch) > 0)
              throw new \RuntimeException(curl_error($ch), curl_errno($ch));
            $content = curl_multi_getcontent($ch);
            if ($info['http_code'] >= 300) {
              throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $content));
            }
            $responseDecoded = $this->decode($content);
            $this->log('Core response data: ' . $this->encode($responseDecoded), 'debug');
            /** @var $callback callback */
            $callback = $this->callbacks[(string)$ch];
            $callback($responseDecoded);
          }
        } elseif ($code != CURLM_CALL_MULTI_PERFORM) {
          throw new \RuntimeException("multi_curl failure [$code]");
        }
      } while ($still_executing);
      $time_end = microtime(true);
      $this->log('Multi-request time:' . ($time_end - $time_start), 'info');
    } catch (Exception $e) {
      $time_end = microtime(true);
      $this->log('Multi-request time:' . ($time_end - $time_start), 'info');
      $error = $e;
    }
    // clear multi container
    foreach ($this->resources as $resource)
      curl_multi_remove_handle($this->multiHandler, $resource);
    curl_multi_close($this->multiHandler);
    $this->multiHandler = null;
    $this->callbacks = array();
    $this->resources = array();
    if (!is_null($error)) {
      $this->log('Error:' . (string)$error . 'Response: ' . print_r(isset($content)?$content:Null, 1), 'error');
      /* @var $error Exception  */
      throw $error;
    }
  }

  /**
   * @param $action
   * @param array $params
   * @param array $data
   * @return resource
   */
  private function createCurlResource($action, array $params = array(), array $data = array())
  {
    $isPostMethod = !empty($data);

    $query = $this->parameters['userapi_url']
      . str_replace('.', '/', $action)
      . '?' . http_build_query(array_merge($params, array('client_id' => $this->parameters['client_code'])));

    $this->log('Send core requset ' . ($isPostMethod ? 'post' : 'get') . ': ' . $query, 'info');
    if ($data)
      $this->log('Request post:' . $this->encode($data), 'info');

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_URL, $query);

    if ($isPostMethod) {
      curl_setopt($connection, CURLOPT_POST, true);
//      curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($data));
    }
    return $connection;
  }

  /**
   * @param $response
   * @return array
   * @throws \RuntimeException
   */
  private function decode($response)
  {
    if (is_null($response)) {
      throw new \RuntimeException('Response cannot be null');
    }
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
      throw new \RuntimeException($errorMessage, $code);
    }

    if (is_array($decoded) && array_key_exists('error', $decoded)) {
      throw new \RuntimeException((string)$decoded['error']['message'] . " " . $this->encode($decoded), (int)$decoded['error']['code']);
    }
    if (array_key_exists('result', $decoded)) {
      $decoded = $decoded['result'];
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
      function($match)
      {
        return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");
      },
      $data
    );
    return $data;
  }

  /**
   * @param array $info
   * @return string
   */
  private function encodeInfo($info)
  {
    return $this->encode(array_intersect_key($info, array_flip(array(
      'content_type', 'http_code', 'header_size', 'request_size',
      'redirect_count', 'total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'size_upload',
      'size_download', 'speed_download',
      'starttransfer_time', 'redirect_time', 'certinfo', 'redirect_url'
    ))));
  }

  private function log($message, $lvl){
//    echo $message."\r\n";
    if(count($this->loggers) > 0){
      foreach($this->loggers as $logger){
        switch($lvl){
          case 'error':
            $logger->error($message);
            break;
          case 'info':
            $logger->info($message);
            break;
          case 'debug':
            $logger->debug($message);
            break;
          case 'trace':
            $logger->trace($message);
            break;
          case 'warning':
            $logger->warn($message);
            break;
          case 'fatal':
            $logger->fatal($message);
            break;
        }
      }
    }
  }
}

class CoreV1Client
{
  /* @var array */
  private $parameters = array();

  /**
   * @var Logger[]
   */
  private $loggers = array();

  private $connection = Null;
  private $coreApiClientId = Null;
  private $coreApiToken = Null;

  private static $instance = Null;

  /**
   * @return CoreV1Client
   */
  static public function getInstance()
  {
    if (is_null(self::$instance)) {
      self::$instance = new CoreV1Client(array('api_url' => Config::get('coreV1APIUrl'), 'consumer_key'=> Config::get('coreV1ConsumerKey'), 'signature'=> Config::get('coreV1Signature')));
    }
    return self::$instance;
  }

  private function __construct(array $parameters)
  {

    $this->parameters = $parameters;

    $this->connection = curl_init();
    curl_setopt($this->connection, CURLOPT_URL, $this->parameters['api_url']);
    curl_setopt ($this->connection, CURLOPT_HEADER, 0);
    curl_setopt($this->connection, CURLOPT_POST, true);
    curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);

    $this->addLogger('CoreClient', Logger::getLogger('CoreClient'));
  }

  private function __clone(){}

  public function addLogger($name, Logger $logger){
    $this->loggers[$name] = $logger;
  }

  public function removeLogger($name){
    $this->loggers[$name] = null;
    unset($this->loggers[$name]);
  }

  public function query($name, array $params = array(), array $data = array())
  {
    $params['uid'] = RequestLogger::getInstance()->getId();
    $action = '/'.str_replace('.', '/', $name).'/';

    if (empty($this->coreApiClientId) || empty($this->coreApiToken))
    {
      $this->auth();
    }

    $data = json_encode(array(
      'action' => $action,
      'param'  => array_merge(array(
        'client_id' => $this->coreApiClientId,
        'token'     => $this->coreApiToken,
      ), $params),
      'data'   => $data), JSON_FORCE_OBJECT);

    $this->log("Request: ".$data, 'info');
    RequestLogger::getInstance()->addLog($name, $params);

    $time_start = microtime(true);
    $response = $this->send($data);
    $this->log("Response: ".$response, 'debug');
    $time_end = microtime(true);
    $this->log('Request time:' . ($time_end - $time_start), 'info');

    $response = json_decode($response, true);

    if (isset($response['error']))
    {
      $message = 'Bad response: '.$response['error']['message'].'('.(isset($response['error']['detail'])?$response['error']['detail']:'no details').'), request action: ' . $action . ', request params: ' . print_r($params, 1) . ', request data: ' . print_r($data, 1) . ', full response: ' . print_r($response, 1);
      $this->log($message, 'error');
      throw new \RuntimeException($message, $response['error']['code']);
    }

    return $response;
  }

  protected function auth()
  {
    $data = json_encode(array(
      'action' => '/auth/',
      'param'  => array(
        'consumer_key'  => $this->parameters['consumer_key'],
        'signature'     => $this->parameters['signature'],
      ),
    ), JSON_FORCE_OBJECT);
    $result = true;

    $this->log('Trying to pass authentification... '. $data, 'info');
    $response = $this->send($data);

    $this->log('Response: '.$response, 'debug');
    $response = json_decode($response, true);

    if (isset($response['error']))
    {
      $this->log('Authentification on V1 core failed', 'info');
      $this->log('Authentification on Core V1 failed ('.$response['error']['code'].'): '.$response['error']['message'], 'error');
      throw new \RuntimeException('Authentification failed: '.$response['error']['message'], $response['error']['code']);
    }
    else
    {
      $this->coreApiClientId = $response['id'];
      $this->coreApiToken = $response['token'];
      $this->log('Authentification on V1 core passed', 'info');
    }

    return $result;
  }

  protected function send($request)
  {
    curl_setopt($this->connection, CURLOPT_POSTFIELDS, $request);
    $response = curl_exec($this->connection);

    if (curl_errno($this->connection) > 0)
    {
      $message = 'Curl error['.curl_errno($this->connection).']: '.curl_error($this->connection);
      $this->log($message, 'error');
      throw new \RuntimeException($message, curl_errno($this->connection));
    }

    return $response;
  }

  private function log($message, $lvl){
    //    echo $message."\r\n";
    if(count($this->loggers) > 0){
      foreach($this->loggers as $logger){
        switch($lvl){
          case 'error':
            $logger->error($message);
            break;
          case 'info':
            $logger->info($message);
            break;
          case 'debug':
            $logger->debug($message);
            break;
          case 'trace':
            $logger->trace($message);
            break;
          case 'warning':
            $logger->warn($message);
            break;
          case 'fatal':
            $logger->fatal($message);
            break;
        }
      }
    }
  }
}