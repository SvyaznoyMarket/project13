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

    // определяет url для редиректа
    $signinUrl = $this->getSigninUrl();

    // если пользователь авторизован
    if ($user->isAuthenticated())
    {
      return $this->redirect($signinUrl);
    }

    $this->form = new UserFormSignin();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        $this->getUser()->signIn($values['user']);

        if ($request->isXmlHttpRequest())
        {
          return $this->renderJson(array(
            'success' => true,
            'user'    => array(
                'email'       => $values['user']['email'],
                'phonenumber' => $values['user']['phonenumber'],
                'first_name'  => $values['user']['first_name'],
                'last_name'   => $values['user']['last_name'],
                'middle_name' => $values['user']['middle_name'],
                'gender'      => $values['user']['gender'],
                'birthday'    => $values['user']['birthday'],
                'address'     => $values['user']['address'],
            ),
          ));
        }
        if ('frame' == $this->getLayout())
        {
          return $this->renderPartial('default/close');
        }
        return $this->redirect($signinUrl);
      }

      if ($request->isXmlHttpRequest())
      {
        return $this->renderJson(array(
          'success' => false,
          'data' => array(
            'content' => $this->getPartial($this->getModuleName().'/form_signin', array('form' => $this->form, 'redirect' => $signinUrl)),
          ),
        ));
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

    if ($request->getParameter('redirect_to'))
    {
      $signoutUrl = $request->getParameter('redirect_to');
    }
    else
    {
      $signoutUrl = sfConfig::get('app_guard_signout_url', $request->getReferer());
    }

    //$this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
    $this->redirect('@homepage');
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
	  if ($request->isXmlHttpRequest())
    {
		  // пробуем достать токен для смены пароля
		  $login = $request->getParameter('login');

      $isEmail = false !== strpos($login, '@');

      $validator = new sfValidatorOr(array(
        new myValidatorEmail(array('required' => true)),
        new myValidatorMobilePhonenumber(array('required' => true)),
      ));

      // validates login as email or phonenumber
      $user = false;
      $error = false;
      try {
        $validator->clean($login);

        $r = CoreClient::getInstance()->query('user/reset-password', array(
          ($isEmail ? 'email' : 'mobile') => $login,
        ));
        if ($r['confirmed'])
        {
          return $this->renderJson(array('success' => true));
        }
      }
      catch (sfValidatorError $e)
      {
        $error = 'Неправильный '.($isEmail ? 'email' : 'телефонный номер');
      }
      catch (Exception $e) {
        $error = 'Неудалось восстановить пароль. Попробуйте позже.';
      }

      return $this->renderJson(array('success' => false, 'error' => $error));
	  }
  }


  public function executeChangePassword($request)
  {
    if (!$this->getUser()->isAuthenticated()) $this->redirect('user_signin');

    $this->form = new UserFormChangePassword($this->user);
  }

  public function executeChangePasswordSave($request)
  {
    if (!$this->getUser()->isAuthenticated()) $this->redirect('user_signin');

    $this->form = new UserFormChangePassword();
    $this->form->bind($request[$this->form->getName()]);

    $this->setTemplate('changePassword');

    if ($this->form->isValid())
    {
      //dump($this->form->getValues(), 1);
      try
      {
        //$this->form->getObject()->setCorePush(false);
        $user = $this->getUser()->getGuardUser();

        $r = CoreClient::getInstance()->query('user/change-password', array(
          'token'        => $user->getToken(),
          'password'     => $this->form->getValue('password_old'),
          'new_password' => $this->form->getValue('password_new'),
        ));

        $this->setTemplate('changePasswordOk');
      }
      catch (Exception $e)
      {
        $this->setVar('error', 'К сожалению, сохранить пароль не удалось.', true);
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
        $this->setTemplate('changePassword');
      }
    }
    else
    {
      $this->setTemplate('changePassword');
    }
  }


 /**
  * Executes changePassword action
  *
  * @deprecated 27.10.2011
  * @param sfRequest $request A request object
  */
  public function executeResetPassword($request)
  {
	  throw new Exception('Deprecated');
    if ($request->isXmlHttpRequest())
    {
      $token = $request->getParameter('token');
      $result = Core::getInstance()->query('user.update-password', array('user_token' => $token));
      if ($result['confirmed'])
      {
        return $this->renderJson(array('success' => true));
      }

      return $this->renderJson(array('success' => false));
    }
  }

  /**
   * Executes register action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeCorporateRegister(sfRequest $request) {
    $form = new CorporateRegisterForm();
    $errors = array();

    if ($request->isMethod('post')) {
      $form->import($request->getPostParameter('register'));

      if ($form->validate()) {

      }

      $errors = $form->getErrors();
    }

    $this->setVar('form', $form);
    $this->setVar('errors', $errors);
  }

  /**
   * Executes register action
   *
   * @param sfRequest $request A request object
   */
  public function executeRegister($request)
  {
    $signinUrl = $this->generateUrl('user', array(), true); // $this->getSigninUrl();

    if ($this->getUser()->isAuthenticated())
    {
      //$this->getUser()->setFlash('notice', 'Вы уже зарегистрированы!');
      $this->redirect('user');
    }

    $this->form = new UserFormRegister();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $username = $this->form->getValue('username');
        $useEmail = false !== strpos($username, '@');

        $data = array(
          'first_name' => $this->form->getValue('first_name'),
        );
        if ($useEmail) {
          $data['email'] = $username;
        }
        else {
          $data['mobile'] = $username;
        }

        try {
          $r = CoreClient::getInstance()->query('user/create', array(), $data);

          if (empty($r['token'])) {
            throw new Exception();
          }

          $data = array(
            'password' => $r['password'],
          );
          if ($useEmail) {
            $data['email'] = $username;
          }
          else {
            $data['mobile'] = $username;
          }
          $r = CoreClient::getInstance()->query('user/auth', $data);
          if (empty($r['token'])) {
            throw new Exception();
          }

          $user = new UserEntity(array(
            'token' => $r['token'],
            'id'    => $r['id'],
          ));
          $this->getUser()->signIn($user);

          return $this->renderJson(array(
            'success' => true,
            'url'     => $signinUrl,
          ));
        }
        catch (Exception $e) {
          switch ($e->getCode()) {
            case 684:
              $message = 'Такой email или номер телефона уже зарегистрирован.';
              break;
            default:
              $message = 'Неправильные данные.';
              break;
          }

          $this->form->getErrorSchema()->addError(new sfValidatorError(new sfValidatorSchema(), $message), 'username');
        }
      }

      return $this->renderJson(array(
        'success' => false,
        'data' => array(
          'content' => $this->getPartial($this->getModuleName().'/form_register', array('form' => $this->form, 'redirect' => $signinUrl)),
        ),
      ));
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
    if (!$this->userProfile)
    {
      return sfView::ERROR;
    }

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
          'email'       => $this->form->getValue('email'),
          'phonenumber' => $this->form->getValue('phonenumber'),
          'last_name'   => $this->userProfile->getLastName(),
          'first_name'  => $this->userProfile->getFirstName(),
          'photo'       => $this->userProfile->getPhoto(),
          'is_active'   => true,
          'region_id'   => $this->getUser()->getRegion('id'),
        ));
        $this->user->save();
        //$this->user->Profile[] = $this->userProfile;
        $this->userProfile->user_id = $this->user->id;
        $this->userProfile->save();
        $this->getUser()->signin($this->user, false);

        if ('frame' == $this->getLayout())
        {
          return $this->renderPartial('default/close');
        }
        //return $this->redirect($this->getSigninUrl());
        return $this->redirect('user');
      }
    }
  }
  /**
   * Executes basicRegister action
   *
   * @param sfRequest $request A request object
   */
  public function executeBasicRegister(sfWebRequest $request)
  {
    $this->form = new UserFormBasicRegister();
    $this->form->bind($request->getParameter($this->form->getName()));

    $return = array(
      'success'  => false,
      'data'     => array(),
      'redirect' => $this->generateUrl('user_orders', array(), true),
    );
    if ($this->form->isValid())
    {
      try {
        $this->user = $this->form->getObject();
        $this->user->is_active = true;
        $this->user = $this->form->save();
        $this->getUser()->signin($this->user, false);
        $return['success'] = true;
      }
      catch(Exception $e) {
      }
    }

    $return['data'] = array('form' => $this->form->render());

    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson($return);
    }

    return $this->redirect('user');
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
      else
      {
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

  protected function getSigninUrl()
  {
    $request = $this->getRequest();

    $signinUrl = !empty($request['redirect_to']) ? $request['redirect_to'] : sfConfig::get('app_guard_signin_url', $this->getUser()->getReferer($request->getReferer()));
    if (empty($signinUrl))
    {
      $signinUrl = '@homepage';
    }

    return $signinUrl;
  }

}
