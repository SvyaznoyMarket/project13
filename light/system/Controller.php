<?php
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
    $className .= 'Controller';
    if(!class_exists($className)){
      if(!file_exists(ROOT_PATH.'controller/'.$classPath.'.php')){
        throw new RuntimeException('request to run unknown Controller '.$className);
      }
      include_once(ROOT_PATH.'controller/'.$classPath.'.php');
    }
    if(!method_exists($className, $methodName)){
      throw new BadMethodCallException('request to run unknown Method '.$methodName.' in controller '.$className);
    }

    $response = App::getResponse();

    call_user_func_array(array($className, $methodName), array(App::getResponse(), $params));

		return $response;
	}

}