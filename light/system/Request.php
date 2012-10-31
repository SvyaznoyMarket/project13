<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 1:26
 * To change this template use File | Settings | File Templates.
 */


/**
 * Класс больше не используется, пока не удаляю, скорее всего, может пригодиться
 */
class Request {
	const
		PORT_HTTP   = 80,
		PORT_HTTPS  = 443,
	  GET         = 'GET',
	  POST        = 'POST',
	  PUT         = 'PUT',
	  DELETE      = 'DELETE',
	  HEAD        = 'HEAD';
	/**
	 * @var Request|null
	 */
	private static $request = null;

	private $uriPrefix = null;
	private $method = null;


	/**
	 * @static
	 * @return Request
	 */
	public static function getInstance(){
		if(is_null(self::$request)){
			self::init();
		}
		return self::$request;
	}

  /**
   * @return bool
   */
  public function isXmlHttpRequest()
  {
    return (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
  }

	public function isAbsUri()
	{
		return isset($_SERVER['REQUEST_URI']) ? preg_match('/^http/', $_SERVER['REQUEST_URI']) : false;
	}

	public function getHost()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	}

	public function getMethod(){
		return $this->method;
	}

	public function getUri()
	{
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

		return $this->isAbsUri() ? $uri : $this->getUriPrefix().$uri;
	}

  public function getReferer(){
    if(isset($_SERVER) && array_key_exists('HTTP_REFERER', $_SERVER)){
      return $_SERVER['HTTP_REFERER'];
    }
    return '';
  }

	public function getUriPrefix()
	{
		if(is_null($this->uriPrefix)){
			$secure = $this->isSecure();

			$protocol = $secure ? 'https' : 'http';
			$host = $this->getHost();
			$port = null;

			// extract port from host or environment variable
			if (false !== strpos($host, ':'))
			{
				list($host, $port) = explode(':', $host, 2);
			}
			else if (isset($_SERVER['SERVER_PORT']))
			{
				$port = $_SERVER['SERVER_PORT'];
			}
			$this->uriPrefix = sprintf('%s://%s%s', $protocol, $host, $port ? ':'.$port : '');
		}
		return $this->uriPrefix;
	}

	/**
	 * Returns true if the current or forwarded request is secure (HTTPS protocol).
	 * @return boolean
	 */
	public function isSecure()
	{
		return
			(isset($_SERVER['HTTPS']) && ('on' == strtolower($_SERVER['HTTPS']) || 1 == $_SERVER['HTTPS']))
			||
			(isset($_SERVER['HTTP_SSL_HTTPS']) && ('on' == strtolower($_SERVER['HTTP_SSL_HTTPS']) || 1 == $_SERVER['HTTP_SSL_HTTPS']))
			||
			$this->isForwardedSecure()
			;
	}

	/**
	 * Returns true if the current request is forwarded from a request that is secure.
	 * @return boolean
	 */
	protected function isForwardedSecure()
	{
		return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
	}

	private static function init(){
		$request = new self();

		$request->setMethod(isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD'] : self::GET);


		self::$request = $request;
	}

	private function __construct(){}
	private function __clone(){}

	private function setMethod($method)
	{
		if (!in_array(strtoupper($method), array(self::GET, self::POST, self::PUT, self::DELETE, self::HEAD)))
		{
      $this->method = self::GET;
		}

		$this->method = strtoupper($method);
	}
}