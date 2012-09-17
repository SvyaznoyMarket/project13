<?php

namespace light;

class SmartengineClient
{
  /* @var array */
  private $config = null;

  /* @var  */
  private $logger = null;

  public function __construct(array $config) {
    $this->config = array_merge(array(
      'api_url'  => null,
      'api_key'  => null,
      'tenantid' => null,
      'timeout'  => 2,
    ), $config);
  }

  /**
   * Run synchronous query.
   *
   * @param $action
   * @param array $params
   * @param array $data
   * @return array
   * @throws SmartengineClientException
   */
  public function query($action, array $params = array())
  {
    $connection = $this->createResource($action, $params);
    $response = curl_exec($connection);
    try {
      if (curl_errno($connection) > 0) {
        throw new SmartengineClientException(curl_error($connection), curl_errno($connection));
      }
      $info = curl_getinfo($connection);
      if ($this->logger) $this->logger->info('Response '.$connection.' : '.(is_array($info) ? json_encode($info) : $info));
      if ($info['http_code'] >= 300) {
        throw new SmartengineClientException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
      }

      if ($this->logger) $this->logger->info('Response data: '.$response);
      $responseDecoded = $this->decode($response);
      curl_close($connection);
      return $responseDecoded;
    }
    catch (SmartengineClientException $e) {
      curl_close($connection);
      if ($this->logger) $this->logger->err($e->__toString());

      throw $e;
    }
  }

  /**
   * @param $action
   * @param array $params
   * @param array $data
   * @return resource
   */
  private function createResource($action, array $params = array())
  {
    foreach ($params as &$param) {
      $param = rawurlencode($param);
    } if (isset($param)) unset($param);

    $params['apikey'] = $this->config['api_key'];
    $params['tenantid'] = $this->config['tenantid'];

    $query = $this->config['api_url']
      . str_replace('.', '/', $action)
      . '?' . http_build_query($params)
    ;

    $connection = curl_init();
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, true);
    if (defined('SMARTENGINE_CERT') && SMARTENGINE_CERT) {
      curl_setopt($connection, CURLOPT_CAINFO, SMARTENGINE_CERT);
    }
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->config['timeout']);
    curl_setopt($connection, CURLOPT_URL, $query);

    if ($this->logger) $this->logger->info('Request '.$connection.' '.'get'.': '.$query);

    return $connection;
  }

  /**
   * @param $response
   * @return array
   * @throws SmartengineClientException
   */
  private function decode($response)
  {
    if (is_null($response)) {
      throw new SmartengineClientException('Response cannot be null');
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

      throw new SmartengineClientException($errorMessage, $code);
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
}


class SmartengineClientException extends \Exception {

}