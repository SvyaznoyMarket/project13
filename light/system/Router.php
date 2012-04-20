<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 11.04.12
 * Time: 0:26
 * To change this template use File | Settings | File Templates.
 */
if(!class_exists('routeException')){
  require_once('exception/routeException.php');
}

class Router{

	private static $rules = array(
		'GET' => array(
			'/(.*)/i' => array(
				'class'  => 'DefaultController',
				'method' => 'index',
				'mapping'=> array(1 => 'pageToken') //@TODO реализовать маппинг в Route
			)
		)

	);

	/**
	 * @static
	 * @param Request $request
	 * @return Route
	 * @throws Exception
	 */
	public static function route(Request $request){
		$method = $request->getMethod();
		if(!isset(self::$rules[$method])){
			throw new routeException (sprintf('cant find route for method: %s.', $method));
		}
		$uri = $request->getUri();
		$uri = str_replace($request->getUriPrefix(), '', $uri);
		foreach(self::$rules[$method] as $rule => $ruleParams){
			if(preg_match($rule, $uri, $matches)){
				return new Route($ruleParams, $matches);
			}
		}
		throw new routeException (sprintf('cant find route for uri: %s.', $uri));
	}
}

class Route{
	private
		$class = null,
		$method = null,
		$params = array();
	public function __construct($rule, $params){
		$this->class = $rule['class'];
		$this->method = $rule['method'];
		$this->params = $params;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getParams()
	{
		return $this->params;
	}
}
