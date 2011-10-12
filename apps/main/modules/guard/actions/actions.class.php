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
    
    private $_validateResult = array();
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
  * Executes signin action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninAjax(sfWebRequest $request)
  {
      
        $this->_request = $request;
        $this->_validateResult['success'] = true;
                       
        
        try{
          //производим валидацию входящих данных
          $this->_validateSigninData();
        }
        catch(Exception $e){
          $this->_validateResult['success'] = false;
          $this->_validateResult['error'] = "Неверные данные";
          return $this->_refuse();
        }
        

        $user = $this->getUser();
        if ($user->isAuthenticated())
        {
          $this->_validateResult['success'] = false;
          $this->_validateResult['error'] = "Вы уже авторизованы.";            
          return $this->_refuse();
        }
        
        //создаём форму авторизации
        $this->form = new UserFormSignin();
        $info['username'] = $this->_request['login'];
        $info['password'] = $this->_request['password'];
        $info['remember'] = $this->_request['remember'];
        $info['_csrf_token'] = $this->form->getCSRFToken(); 

        $this->form->bind($info);

        if (!$this->form->isValid()){
          $this->_validateResult['success'] = false;
          $this->_validateResult['error'] = "Авторизация не прошла.";            
          return $this->_refuse();            
        }
        //сама авторизация
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'], $this->_request['remember']);
        
        
        return $this->renderJson(array(
        'success' => $this->_validateResult['success'],
        'data'    => array(
          'user_id' => $this->getUser()->getGuardUser()->id,
          'user_name' => $this->getUser()->getName(),
        ),
        ));                          
       
  }
  
  private function _refuse(){
      return $this->renderJson(array(
        'success' => $this->_validateResult['success'],
        'data'    => array(
          'error' => $this->_validateResult['error'],
        ),
      ));        
  }
  
  private function _validateSigninData()
  {
      if (!$this->_request['login']){
          $this->_validateResult['error'] = 'Необходимо указать логин';
      }
      if (!$this->_request['password']){
          $this->_validateResult['error'] = 'Необходимо указать пароль';
      }
      if (isset($this->_validateResult['error'])) $this->_validateResult['success'] = false;
  
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
  * Executes register action
  *
  * @param sfRequest $request A request object
  */
  public function executeRegister($request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->getUser()->setFlash('notice', 'Вы уже зарегистрированы!');
      $this->redirect('@homepage');
    }

    $this->form = new UserFormRegister();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $user = $this->form->getObject();

        $user->is_active = true;
        $user->email = $this->form->getValue('email');
        $user->phonenumber = $this->form->getValue('phonenumber');

        $user->setPassword('123456');

        $user = $this->form->save();
        //$user->refresh();
        $this->getUser()->signIn($user);


        // event: { password: generate() }

        $this->redirect('@homepage');
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
          'nickname'   => $this->userProfile->getNickname(),
          'last_name'  => $this->userProfile->getLastName(),
          'first_name' => $this->userProfile->getFirstName(),
          'photo'      => $this->userProfile->getPhoto(),
          'is_active'  => true,
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
