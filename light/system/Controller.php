<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 1:34
 * To change this template use File | Settings | File Templates.
 */


class Controller {

	/**
	 * @static
	 * @param Route $route
	 * @param Request $request
	 * @return Response
	 */
	public static function Run(Route $route){

    if(!class_exists($route->getClass())){
      if(!file_exists(ROOT_PATH.'controller/'.$route->getClass().'.php')){
        throw new routeException('request to run unknown Controller '.$route->getClass());
      }
      include_once(ROOT_PATH.'controller/'.$route->getClass().'.php');
    }
    if(!method_exists($route->getClass(),$route->getMethod())){
      throw new routeException('request to run unknown Method '.$route->getMethod().' in controller '.$route->getClass());
    }

    $response = new Response();

    call_user_func_array(array($route->getClass(), $route->getMethod()), array($route->getParams(), $response));

		return $response;
	}
}