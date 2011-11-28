<?php

/**
 * default actions.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
 /**
  * Executes welcome action
  *
  * @param sfRequest $request A request object
  */
  public function executeWelcome(sfWebRequest $request)
  {
    if (!sfConfig::get('app_welcome_enabled', false))
    {
      //$this->redirect('@homepage', 301);
    }

    $this->setLayout('welcome');

    $cookieName = sfConfig::get('app_welcome_cookie_name');
    $secret = sfConfig::get('app_welcome_secret');
    if ($request->isMethod('post'))
    {
      if ($secret == $request->getParameter($cookieName))
      {
        $this->getResponse()->setCookie($cookieName, md5($secret));
        $this->redirect($request['url'] ? $request['url'] : '@homepage');
      }
    }

    $this->url = $request['url'] ? $request['url'] : $request->getUri();
  }
 /**
  * Executes error404 action
  *
  * @param sfRequest $request A request object
  */
  public function executeError404(sfWebRequest $request)
  {
	  $this->setLayout(false);
  }
 /**
  * Executes redirect action
  *
  * @param sfRequest $request A request object
  */
  public function executeRedirect(sfWebRequest $request)
  {
    $route = $request['route'];
    $this->forward404Unless($route);

    $params = array();
    foreach ($request->getRequestParameters() as $k => $v)
    {
      if (in_array($k, array('action', 'module', 'route')) || (0 === strpos($k, '_sf_'))) continue;
      $params[$k] = $v;
    }

    $this->redirect('@'.$route.'?'.http_build_query($params), 301);
  }
}
