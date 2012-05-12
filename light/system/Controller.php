<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 1:34
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/exception/routerException.php');

class Controller {

	/**
	 * @static
	 * @param string $route
	 * @return Response
	 */
	public static function Run($route){

    list($className, $methodName) = explode('->', $route);

    if(!class_exists($className)){
      if(!file_exists(ROOT_PATH.'controller/'.$className.'.php')){
        throw new routerException('request to run unknown Controller '.$className);
      }
      include_once(ROOT_PATH.'controller/'.$className.'.php');
    }
    if(!method_exists($className, $methodName)){
      throw new routerException('request to run unknown Method '.$methodName.' in controller '.$className);
    }

    $response = new Response();

    call_user_func_array(array($className, $methodName), array($response));

		return $response;
	}

}