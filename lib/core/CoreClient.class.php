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

    /** @var resource */
  private $multiHandler;
  /** @var callback[] */
  private $callbacks = array();
  private $resources = array();
  private $isOnExecute = false;

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
    $this->logger->addLogger(sfContext::getInstance()->getLogger());

    $this->still_executing = false;
  }

  /**
   * Run synchronous query.
   *
   * @param $action
   * @param array $params
   * @param array $data
   * @return array
   * @throws CoreClientException
   */
  public function query($action, array $params = array(), array $data = array())
  {
    $params['uid'] = RequestLogger::getInstance()->getId();
    $connection = $this->createCurlResource($action, $params, $data);
    $response = curl_exec($connection);
    try {
      if (curl_errno($connection) > 0) {
        throw new CoreClientException(curl_error($connection), curl_errno($connection));
      }
      $info = curl_getinfo($connection);
      RequestLogger::getInstance()->addLog($info['url'], print_r($data, true), $info['total_time']);
      if ($this->parameters->get('log_enabled')) {
        $this->logger->info('Core response '.$connection.' : ' . $this->encodeInfo($info));
      }
      if ($info['http_code'] >= 300) {
        throw new CoreClientException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
      }
      $responseDecoded = $this->decode($response);
      if ($this->parameters->get('log_data_enabled')) {
        $this->logger->info('Core response data: ' . $this->encode($responseDecoded));
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
    $params['uid'] = RequestLogger::getInstance()->getId();
    $resource = $this->createCurlResource($action, $params, $data);
    curl_multi_add_handle($this->multiHandler, $resource);
    $this->callbacks[(string)$resource] = $callback;
    $this->resources[] = $resource;
    $this->still_executing = true;
  }

  /**
   * Run all added query in parallel mode.
   * Callbacks are called in the order of answers http, and not in order of call addQuery.
   * Note, application is blocked until the processing of all requests.
   *
   * @see addQuery
   * @throws CoreClientException
   */
  public function execute()
  {
    if($this->isOnExecute){
      if ($this->parameters->get('log_enabled')) {
        $this->logger->err('Call execute at callback - ignored');
      }
      return;
    }
    if (!is_resource($this->multiHandler))
      throw new CoreClientException('No query to execute');

    $active = null;
    $error = null;
    try {
        do {
            do {
              $code = curl_multi_exec($this->multiHandler, $curl_still_executing);
              $this->still_executing = $curl_still_executing;
            } while ($code == CURLM_CALL_MULTI_PERFORM);

          // if one or more descriptors is ready, read content and run callbacks
            while ($done = curl_multi_info_read($this->multiHandler)) {

            $ch = $done['handle'];
            $info = curl_getinfo($ch);

            RequestLogger::getInstance()->addLog($info['url'], "unknown in multi curl", $info['total_time']);

            if ($this->parameters->get('log_enabled')) {
              $this->logger->info('Core response '.$ch.' done: ' . $this->encodeInfo($info));
            }
            if (curl_errno($ch) > 0)
              throw new CoreClientException(curl_error($ch), curl_errno($ch));
            $content = curl_multi_getcontent($ch);
            if ($info['http_code'] >= 300) {
              throw new CoreClientException(sprintf(
                "Invalid http code: %d, \nResponse: %s, %s",
                $info['http_code'],
                $content,
                print_r($info,1)
              ));
            }
            $responseDecoded = $this->decode($content);
            if ($this->parameters->get('log_data_enabled')) {
              $this->logger->info('Core response data: ' . $this->encode($responseDecoded));
            }
            /** @var $callback callback */
            $callback = $this->callbacks[(string)$ch];
            $callback($responseDecoded);
            }
            if ($curl_still_executing) {
	            $ready = curl_multi_select($this->multiHandler);
	        }
        } while ($this->still_executing);
    } catch (Exception $e) {
      $error = $e;
    }

    if ($this->parameters->get('log_enabled')) {
      $this->logger->info('Close current multiquery queue');
    }

    // clear multi container
    foreach ($this->resources as $resource)
      curl_multi_remove_handle($this->multiHandler, $resource);
    curl_multi_close($this->multiHandler);
    $this->multiHandler = null;
    $this->callbacks = array();
    $this->resources = array();
    if ($error) {
      if ($this->parameters->get('log_enabled')) {
        $this->logger->err((string)$error);
      }
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

    $query = $this->parameters->get('userapi_url')
      . str_replace('.', '/', $action)
      . '?' . http_build_query(array_merge($params, array('client_id' => $this->parameters->get('client_code'))));

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_URL, $query);

    if ($isPostMethod) {
      curl_setopt($connection, CURLOPT_POST, true);
      curl_setopt($connection, CURLOPT_POSTFIELDS, $this->encode($data));
    }

    if ($this->parameters->get('log_enabled')) {
      $this->logger->info('Send core request ' . $connection.' '. ($isPostMethod ? 'post' : 'get') . ': ' . $query);
      if ($data)
        $this->logger->info('Request post:' . $this->encode($data));
    }
    return $connection;
  }

  /**
   * @param $response
   * @return array
   * @throws CoreClientException
   */
  private function decode($response)
  {
    if (is_null($response)) {
      throw new CoreClientException('Response cannot be null');
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
      throw new CoreClientException($errorMessage, $code);
    }

    if (is_array($decoded) && array_key_exists('error', $decoded)) {
      throw new CoreClientException((string)$decoded['error']['message'] . " " . $this->encode($decoded), (int)$decoded['error']['code']);
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
}
