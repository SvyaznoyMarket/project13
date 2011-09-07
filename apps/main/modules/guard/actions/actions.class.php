<?php

/**
 * guard actions.
 *
 * @package    enter
 * @subpackage guard
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class guardActions extends myActions
{
 /**
  * Executes signin action
  *
  * @param sfRequest $request A request object
  */
  public function executeSignin($request)
  {
    if ($request['error'])
    {
      return sfView::ERROR;
    }

    $providerName = $request->getParameter('provider', false);
    $this->forwardIf($providerName, $this->getModuleName(), 'oauthSignin');

    $user = $this->getUser();
    if ($user->isAuthenticated())
    {
      return $this->redirect('@homepage');
    }

    $this->form = new UserFormSignin();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

        // always redirect to a URL set in app.yml
        // or to the referer
        // or to the homepage
        $signinUrl = sfConfig::get('app_guard_signin_url', $user->getReferer($request->getReferer()));

        if ('frame' == $this->getLayout())
        {
          return $this->renderPartial('default/close');
        }
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
  * Executes forgotPassword action
  *
  * @param sfRequest $request A request object
  */
  public function executeForgotPassword($request)
  {

  }
 /**
  * Executes changePassword action
  *
  * @param sfRequest $request A request object
  */
  public function executeChangePassword($request)
  {
    $this->user = $this->getUser()->getGuardUser();
    $this->form = new UserFormChangePassword($this->user);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        //$this->_deleteOldUserForgotPasswordRecords();

        $this->dispatcher->notify(new myEvent($this, 'user.change_password', array(
          'user' => $this->user,
        )));


        $this->getUser()->setFlash('notice', 'Пароль успешно обновлен');
        $this->redirect('@user_signin');
      }
    }
  }
 /**
  * Executes quickRegister action
  *
  * @param sfRequest $request A request object
  */
  public function executeQuickRegister($request)
  {
    $this->userProfile = $this->getUser()->getProfile();

    $this->user = new User();
    $this->user->email = $this->userProfile->getEmail();

    $this->form = new UserFormQuickRegister($this->user);

    if ($request->isMethod('post'))
    {
      if (!$this->userProfile)
      {
        if ('frame' == $this->getLayout())
        {
          return $this->renderText('<h1>Вход</h1><p>Время сессии истекло. Попробуйте авторизоваться заново.</p>');
        }
        return $this->redirect('user_signin');
      }

      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->user = new User();
        $this->user->fromArray(array(
          'email'      => $this->form->getValue('email'),
          'username'   => $this->form->getValue('email'),
          'last_name'  => $this->userProfile->getLastName(),
          'first_name' => $this->userProfile->getFirstName(),
          'photo'      => $this->userProfile->getPhoto(),
        ));
        $this->user->Profile[] = $this->userProfile;
        $this->user->save();
        $this->getUser()->signin($this->user, false);

        if ('frame' == $this->getLayout())
        {
          return $this->renderPartial('default/close');
        }
        return $this->redirect('@homepage');
      }
    }
  }
 /**
  * Executes oauthSignin action
  *
  * @param sfRequest $request A request object
  */
  public function executeOauthSignin(sfWebRequest $request)
  {
    $provider = $this->getProvider($request['provider']);

    $url = $provider->getSigninUrl();
    $this->redirect($url);
  }
 /**
  * Executes oauthCallback action
  *
  * @param sfRequest $request A request object
  */
  public function executeOauthCallback(sfWebRequest $request)
  {
    $this->setLayout(false);

    $provider = $this->getProvider($request['provider']);

    if ($userProfile = $provider->getUserProfile($request, $this->getUser()))
    {
      $this->setLayout(false);
      if ($userProfile->exists())
      {
        $this->getUser()->signin($userProfile->User);

        return $this->renderPartial('default/close', array('url' => $this->generateUrl('user', array(), true)));
      }
      else {
        $this->getUser()->setProfile($userProfile);

        return $this->renderPartial('default/close', array('url' => $this->generateUrl('user_quickRegister', array(), true)));
      }
    }

    return $this->renderPartial('default/close', array(
      'url' => $this->generateUrl('user_signin', array('error' => 'reject')),
    ));
  }

  protected function getProvider($name = null)
  {
    if (null == $name)
    {
      $name = $this->getRequestParameter('provider');
    }

    $class = sfInflector::camelize('open_auth_'.$name.'_provider');
    $this->forward404Unless(!empty($name) && class_exists($class));

    $providers = sfConfig::get('app_open_auth_provider');

    return new $class($providers[$name]);
  }
}
