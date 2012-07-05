<?php
require_once(ROOT_PATH.'lib/CoreClient.php');
require_once(ROOT_PATH.'system/Response.php');

class App{

  /**
   * @var Router
   */
  private static $Router = Null;
  /**
   * @var Renderer
   */
  private static $Renderer = Null;

  /**
   * @var array
   */
  private static $modelCollection = array();

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
   * @return Router
   */
  public static function getRouter(){
    if(!is_null(self::$Router)){
      return self::$Router;
    }

    if(!class_exists('Router')){
      require_once(ROOT_PATH.'system/Router.php');
    }
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
   * @return Renderer
   */
  public static function getRenderer(){
    if(is_null(self::$Renderer)){
      if(!class_exists('Renderer')){
        require_once(ROOT_PATH.'system/Renderer.php');
      }
      self::$Renderer = new Renderer();
    }
    return self::$Renderer;
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
    if(!class_exists($className)){
      if(file_exists(ROOT_PATH.'model/'.$className.'.php')){
        require_once(ROOT_PATH.'model/'.$className.'.php');
        if(!class_exists($className)){
          throw new RuntimeException('class '.$className.' not exists');
        }
      }
      else{
        throw new RuntimeException('controller '.$className.' not exists');
      }
    }

    if(!isset(self::$modelCollection[$className])){
      self::$modelCollection[$className] = new $className();
    }
    return self::$modelCollection[$className];
  }
}
