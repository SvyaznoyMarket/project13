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
    $this->forward('task', 'index');
  }
 /**
  * Executes login action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogin(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      if ($request['secret'] == sfConfig::get('sf_csrf_secret'))
      {
        $this->getUser()->setAuthenticated(true);

        $this->redirect('homepage');
      }
    }
  }
 /**
  * Executes logout action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogout(sfWebRequest $request)
  {
    $this->getUser()->setAuthenticated(false);

    $this->redirect('homepage');
  }
}
