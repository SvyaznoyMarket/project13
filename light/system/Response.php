<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 2:27
 * To change this template use File | Settings | File Templates.
 */
class Response
{
  const DEFAULT_STATUS        = 200;
  const PROTOCOL              = 'HTTP/1.0';
  const DEFAULT_CHARSET       = 'utf-8';
  const DEFAULT_CONTENT_TYPE  = 'text/html';

  private $content = '';
  private $headers = array();
  private $statusCode = self::DEFAULT_STATUS;
  private $statusText = 'OK';
  private $cookies = array();

  static protected $statusTexts = array(
    '100' => 'Continue',
    '101' => 'Switching Protocols',
    '200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',
    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '306' => '(Unused)',
    '307' => 'Temporary Redirect',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '402' => 'Payment Required',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Timeout',
    '409' => 'Conflict',
    '410' => 'Gone',
    '411' => 'Length Required',
    '412' => 'Precondition Failed',
    '413' => 'Request Entity Too Large',
    '414' => 'Request-URI Too Long',
    '415' => 'Unsupported Media Type',
    '416' => 'Requested Range Not Satisfiable',
    '417' => 'Expectation Failed',
    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Timeout',
    '505' => 'HTTP Version Not Supported',
  );

  public static function getInstance(){
    static $instance;
    if (!$instance) {
      $instance = new Response();
    }
    return $instance;
  }

  private function __construct(){
    $this->setContentType(self::DEFAULT_CONTENT_TYPE);
  }

	public function sendHeaders(){
    header(self::PROTOCOL.' '.$this->statusCode.' '.$this->statusText);

    foreach ($this->headers as $name => $value)
    {
      header($name.': '.$value);
    }

    foreach ($this->cookies as $cookie)
    {
      setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
    }
  }

  public function getContent(){
    return $this->content;
  }

	public function sendContent(){
    echo $this->getContent();
  }

  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Gets HTTP header current value.
   *
   * @param  string $name     HTTP header name
   * @param  string $default  Default value returned if named HTTP header is not found
   *
   * @return string
   */
  public function getHttpHeader($name, $default = null)
  {
    $name = $this->normalizeHeaderName($name);

    return isset($this->headers[$name]) ? $this->headers[$name] : $default;
  }

  /**
   * Retrieves HTTP headers from the current web response.
   *
   * @return string HTTP headers
   */
  public function getHttpHeaders()
  {
    return $this->headers;
  }

  public function setContent($content){
    if(!is_string($content)){
      throw new Exception('content must be a string');
    }
    $this->content = $content;
  }

  public function setStatusCode($code, $name = null)
  {
    $this->statusCode = $code;
    $this->statusText = null !== $name ? $name : self::$statusTexts[$code];
  }

  /**
   * Sets a HTTP header.
   *
   * @param string  $name     HTTP header name
   * @param string  $value    Value (if null, remove the HTTP header)
   * @param bool    $replace  Replace for the value
   *
   */
  public function setHttpHeader($name, $value, $replace = true)
  {
    $name = $this->normalizeHeaderName($name);

    if (null === $value)
    {
      unset($this->headers[$name]);

      return;
    }

    if ('Content-Type' == $name)
    {
      if ($replace || !$this->getHttpHeader('Content-Type', null))
      {
        $this->setContentType($value);
      }

      return;
    }

    if (!$replace)
    {
      $current = isset($this->headers[$name]) ? $this->headers[$name] : '';
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->headers[$name] = $value;
  }

  /**
   * Checks if response has given HTTP header.
   *
   * @param  string $name  HTTP header name
   *
   * @return bool
   */
  public function hasHttpHeader($name)
  {
    return array_key_exists($this->normalizeHeaderName($name), $this->headers);
  }

  /**
   * Sets response content type.
   *
   * @param string $value  Content type
   *
   */
  public function setContentType($value)
  {
    $this->headers['Content-Type'] = $this->fixContentType($value);
  }

  public function clearHttpHeaders()
  {
    $this->headers = array();
  }

  /**
   * Sets a cookie.
   *
   * @param  string  $name      HTTP header name
   * @param  string  $value     Value for the cookie
   * @param  string  $expire    Cookie expiration period
   * @param  string  $path      Path
   * @param  string  $domain    Domain name
   * @param  bool    $secure    If secure
   * @param  bool    $httpOnly  If uses only HTTP
   *
   * @throws <b>sfException</b> If fails to set the cookie
   */
  public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
  {
    if ($expire !== null)
    {
      if (is_numeric($expire))
      {
        $expire = (int) $expire;
      }
      else
      {
        $expire = strtotime($expire);
        if ($expire === false || $expire == -1)
        {
          throw new Exception('Your expire parameter is not valid.');
        }
      }
    }

    $this->cookies[$name] = array(
      'name'     => $name,
      'value'    => $value,
      'expire'   => $expire,
      'path'     => $path,
      'domain'   => $domain,
      'secure'   => $secure ? true : false,
      'httpOnly' => $httpOnly,
    );
  }

  /**
   * Fixes the content type by adding the charset for text content types.
   *
   * @param  string $contentType  The content type
   *
   * @return string The content type with the charset if needed
   */
  protected function fixContentType($contentType)
  {
    // add charset if needed (only on text content)
    if (false === stripos($contentType, 'charset') && (0 === stripos($contentType, 'text/') || strlen($contentType) - 3 === strripos($contentType, 'xml')))
    {
      $contentType .= '; charset='.self::DEFAULT_CHARSET;
    }

    return $contentType;
  }

  protected function normalizeHeaderName($name)
  {
    return preg_replace('/\-(.)/e', "'-'".strtoupper('\\1'), strtr(ucfirst(strtolower($name)), '_', '-'));
  }
}
