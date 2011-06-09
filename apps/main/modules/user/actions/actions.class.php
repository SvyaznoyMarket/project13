<?php

/**
 * user actions.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends sfActions
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
  * Executes signin action
  *
  * @param sfRequest $request A request object
  */
  public function executeSignin($request)
  {
    $user = $this->getUser();
    if ($user->isAuthenticated())
    {
      return $this->redirect('@homepage');
    }

    $this->form = new UserFormSignin();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

        // always redirect to a URL set in app.yml
        // or to the referer
        // or to the homepage
        $signinUrl = sfConfig::get('app_guard_signin_url', $user->getReferer($request->getReferer()));

        return $this->redirect('' != $signinUrl ? $signinUrl : '@homepage');
      }
    }
    else
    {
      if ($request->isXmlHttpRequest())
      {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(401);

        return sfView::NONE;
      }

      // if we have been forwarded, then the referer is the current URL
      // if not, this is the referer of the current request
      $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

      $module = sfConfig::get('sf_login_module');
      if ($this->getModuleName() != $module)
      {
        return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
      }

      $this->getResponse()->setStatusCode(401);
    }
  }
 /**
  * Executes signout action
  *
  * @param sfRequest $request A request object
  */
  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('app_guard_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }
 /**
  * Executes secure action
  *
  * @param sfRequest $request A request object
  */
  public function executeSecure($request)
  {
    $this->getResponse()->setStatusCode(403);
  }
 /**
  * Executes password action
  *
  * @param sfRequest $request A request object
  */
  public function executePassword($request)
  {
    throw new sfException('This method is not yet implemented.');
  }
 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
  }
}
