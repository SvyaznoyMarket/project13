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
    sfConfig::set('sf_web_debug', false); // важно!

    if (!sfConfig::get('app_welcome_enabled', false))
    {
      //$this->redirect('@homepage', 301);
    }

    $this->setLayout('welcome');

    $cookieName = sfConfig::get('app_welcome_cookie_name');
    $secret = sfConfig::get('app_welcome_secret');
    if ($request->isMethod('post'))
    {
      if (
        ($secret == $request->getParameter($cookieName))
        || (sfConfig::get('sf_csrf_secret') == $request->getParameter($cookieName))
      ) {
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
}
