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
    if ($route)
    {
      $params = array();
      switch ($route)
      {
        case 'productCard': case 'changeProduct': case 'productComment':
          $product = ProductTable::getInstance()->findOneByBarcode($request['product']);
          if (!$product)
          {
            $this->forward404();
          }

          $params['product'] = $product->token_prefix.'/'.$product->token;
          break;
      }

      foreach ($request->getRequestParameters() as $k => $v)
      {
        if (in_array($k, array('action', 'module', 'route')) || (0 === strpos($k, '_sf_'))) continue;
        if (array_key_exists($k, $params)) continue;
        $params[$k] = $v;
      }

      $this->redirect('@'.$route.'?'.http_build_query($params), 301);
    }
    else {
      $url = urldecode($request->getPathInfo());
      //попытка вычленить из пути _filter и _tag для каталога
      $matches = array();
      preg_match('/(?<url>.*\/)(?<special>_.*)?$/', $url, $matches);

      $redirect = RedirectTable::getInstance()->getByUrl($matches['url']);
      $this->forward404Unless($redirect);
      $getParameters = $request->getGetParameters();
      $this->redirect((sfConfig::get('sf_no_script_name') ? '' : $request->getScriptName()).$redirect['new_url'].(isset($matches['special']) ? $matches['special'] : '').(count($getParameters) ? ('?'.http_build_query($getParameters)) : ''), $redirect['status_code']);
    }
  }
}
