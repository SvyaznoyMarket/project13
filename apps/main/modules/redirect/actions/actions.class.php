<?php

/**
 * redirect actions.
 *
 * @package    enter
 * @subpackage redirect
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class redirectActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $route = $request['route'];
    $this->forward404Unless($route);

    $routes = array(
      'default' => function($action) use ($request) {
        $return = array();
        foreach ($request->getRequestParameters() as $k => $v)
        {
          if (in_array($k, array('action', 'module', 'route')) || (0 === strpos($k, '_sf_'))) continue;
          $return[$k] = $v;
        }

        return $return;
      },
      'productCard' => function($action) use ($request) {
        $product = ProductTable::getInstance()->findOneByBarcode($request['product']);
        if (!$product)
        {
          $action->forward404();
          return false;
        }

        return array('product' => $product->token);
      }
    );

    $params = call_user_func_array(isset($routes[$route]) ? $routes[$route] : $routes['default'], array($this));

    $this->redirect('@'.$route.'?'.http_build_query($params), 301);
  }
}
