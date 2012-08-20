<?php
namespace light;
use Logger;
require_once(ROOT_PATH.'lib/coreClient/CoreClient.php');
require_once(ROOT_PATH.'lib/WPRequest.php');
require_once(ROOT_PATH.'system/Response.php');
require_once(ROOT_PATH.'system/Request.php');
require_once(ROOT_PATH.'lib/log4php/Logger.php');
require_once 'filler.php';

class App{

  /**
   * @var Router
   */
  private static $Router = Null;

  private static $sessionStarted = false;

  private static $filler;

  /**
   * @var array
   */
  private static $modelCollection = array();

  public static function init(){
    $cookieDefaults = session_get_cookie_params();

    $options = array(
      'session_name'            => SESSION_NAME,
      'session_id'              => null,
      'auto_start'              => true,
      'session_cookie_lifetime' => is_null(SESSION_COOKIE_LIFETIME)? $cookieDefaults['lifetime'] : SESSION_COOKIE_LIFETIME,
      'session_cookie_path'     => $cookieDefaults['path'],
      'session_cookie_domain'   => $cookieDefaults['domain'],
      'session_cookie_secure'   => $cookieDefaults['secure'],
      'session_cookie_httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
      'session_cache_limiter'   => null,
    );


    // set session name
    $sessionName = $options['session_name'];
    session_name($sessionName);

    $lifetime = $options['session_cookie_lifetime'];
    $path     = $options['session_cookie_path'];
    $domain   = $options['session_cookie_domain'];
    $secure   = $options['session_cookie_secure'];
    $httpOnly = $options['session_cookie_httponly'];
    session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);

    if (!self::$sessionStarted && !isset($_SESSION))
    {
      session_start();
      self::$sessionStarted = true;
    }

    Logger::configure(LOGGER_CONFIG_PATH); //В отдельную константу вынесено - что бы можно было иметь разные конфиги для dev и prod

    $filler = Filler::getInstance();
    $filler->setFilePath(VIEW_PATH . 'filler');
    self::$filler = $filler;
  }

  /**
   * @static
   * @throws RuntimeException
   * @return DeliveryModel
   */
  public static function getDelivery(){
    return self::loadModel('DeliveryModel');
  }

  /**
   * @static
   * @throws RuntimeException
   * @return CategoryModel
   */
  public static function getCategory(){
    return self::loadModel('CategoryModel');
  }

  /**
   * @static
   * @return RegionModel
   */
  public static function getRegion(){
    return self::loadModel('RegionModel');
  }

  /**
   * @static
   * @return PromoModel
   */
  public static function getPromo(){
    return self::loadModel('PromoModel');
  }

  /**
   * @static
   * @return ProductModel
   */
  public static function getProduct(){
    return self::loadModel('ProductModel');
  }

  /**
   * @static
   * @return ServiceModel
   */
  public static function getService(){
    return self::loadModel('ServiceModel');
  }

  /**
   * @static
   * @return Router
   */
  public static function getRouter(){
    if(!is_null(self::$Router)){
      return self::$Router;
    }

    require_once(ROOT_PATH.'system/Router.php');

    self::$Router = Router::fromArray(require(ROOT_PATH.'config/routes.php'));
    return self::$Router;
  }

  /**
   * @static
   * @return Response
   */
  public static function getResponse(){
    return Response::getInstance();
  }

  /**
   * @static
   * @return Request
   */
  public static function getRequest(){
    return Request::getInstance();
  }

  /**
   * @static
   * @return Renderer
   */
  public static function getRenderer(){
    if(!class_exists('Renderer')){
      require_once(ROOT_PATH.'system/Renderer.php');
    }

    return Renderer::getInstance();
  }

  /**
   * @static
   * @return HtmlRenderer
   */
  public static function getHtmlRenderer(){
    if(!class_exists('HtmlRenderer')){
      require_once(ROOT_PATH.'system/Renderer.php');
    }

    return HtmlRenderer::getInstance();
  }

  /**
   * @static
   * @return CoreV1Client
   */
  public static function getCoreV1(){
    return CoreV1Client::getInstance();
  }

  /**
   * @static
   * @return CoreClient
   */
  public static function getCoreV2(){
    return CoreClient::getInstance();
  }

  /**
   * @static
   * @return CurrentUser
   */
  public static function getCurrentUser(){
    require_once(ROOT_PATH.'lib/CurrentUser.php');
    return CurrentUser::getInstance();
  }

  private static function loadModel($className){
    $fileName = $className;
    $className = "light\\".$className;
    if(!class_exists($className)){
      if(file_exists(ROOT_PATH.'model/'.$fileName.'.php')){
        require_once(ROOT_PATH.'model/'.$fileName.'.php');
        if(!class_exists($className)){
          throw new \RuntimeException('class '.$className.' not exists');
        }
      }
      else{
        throw new \RuntimeException('controller '.$className.' not exists');
      }
    }

    if(!isset(self::$modelCollection[$className])){
      self::$modelCollection[$className] = new $className();
    }
    return self::$modelCollection[$className];
  }

  public static function getFiller($fillerName)
  {
      return self::$filler->get($fillerName);
  }
}
