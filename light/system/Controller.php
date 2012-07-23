<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 1:34
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/App.php');

class Controller {

	/**
	 * @static
	 * @param string $route
	 * @return Response
	 */
	public static function Run($route, $params=array()){

    list($className, $methodName) = explode('.', $route);
    $classPath = $className;
    $className = 'light\\'.$className.'Controller';
    if(!class_exists($className)){
      if(!file_exists(ROOT_PATH.'controller/'.$classPath.'.php')){
        throw new \RuntimeException('request to run unknown Controller '.$className);
      }
      include_once(ROOT_PATH.'controller/'.$classPath.'.php');
    }
    if(!class_exists($className)){
      throw new \RuntimeException('request to run unknown Controller '.$className);
    }
    if(!method_exists($className, $methodName)){
      throw new \BadMethodCallException('request to run unknown Method '.$methodName.' in controller '.$className);
    }

    $response = App::getResponse();

    if(!$response->hasHttpHeader('Last-Modified')){
      $response->setHttpHeader('Last-Modified', Response::getDate(time()));
    }
    $controller = new $className();

    call_user_func_array(array($controller, $methodName), array(App::getResponse(), $params));

		return $response;
	}

}