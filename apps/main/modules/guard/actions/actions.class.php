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
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

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
                'nickname'    => $values['user']['nickname'],
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

      $loginType = (false !== strpos($login, '@')) ? 'email' : 'phonenumber';

      $validator = new sfValidatorOr(array(
        new myValidatorEmail(array('required' => true)),
        new myValidatorMobilePhonenumber(array('required' => true)),
      ));

      // validates login as email or phonenumber
      $user = false;
      $error = false;
      try {
        $validator->clean($login);

        if ('email' == $loginType)
        {
          $user = UserTable::getInstance()->retrieveByEmail($login);
        }
        else
        {
          $user = UserTable::getInstance()->retrieveByPhonenumber($login);
        }

        if (!$user)
        {
          $error = 'Пользователь с таким '.('email' == $loginType ? 'email' : 'телефонным номером').' не найден';
        }
      }
      catch (Exception $e)
      {
        $user = false;
        $error = 'Неправильный '.('email' == $loginType ? 'email' : 'телефонный номер');
      }

		  if ($user)
      {
			  //$result = Core::getInstance()->query('user.get-password-token', array('id' => $user->core_id));
			  $result = Core::getInstance()->query('user.reset-password', array('id' => $user->core_id));
			  if ($result['confirmed'])
        {
				  return $this->renderJson(array('success' => true));
			  }
		  }

		  return $this->renderJson(array('success' => false, 'error' => $error));
	  }
  }


  public function executeChangePassword($request)
  {
    if (!$this->getUser()->isAuthenticated()) $this->redirect('user_signin');

    $this->getUser()->getGuardUser()->refresh();
    $this->userProfile = $this->getUser()->getGuardUser()->getData();
    $this->form = new UserFormChangePassword($this->getUser()->getGuardUser());
  }

  public function executeChangePasswordSave($request)
  {
    if (!$this->getUser()->isAuthenticated()) $this->redirect('user_signin');

    $this->getUser()->getGuardUser()->refresh();
    $this->userProfile = $this->getUser()->getGuardUser()->getData();
    $this->form = new UserFormChangePassword( $this->getUser()->getGuardUser() );
    $data = $request->getParameter($this->form->getName());
    $this->form->bind($data);

    $this->setTemplate('changePassword');

    if ($this->form->isValid())
    {
      try
      {
        //$this->form->getObject()->setCorePush(false);
        $user = $this->getUser()->getGuardUser();
        $coreId = $user->core_id;
        $data['email'] = $user->email;
        $data['mobile'] = $user->phonenumber;
        #print_r($data);
        //$result = $this->form->save();
        #var_dump($result);
        Core::getInstance()->changePassword($coreId,$data);
        #if (!$result) $this->setVar('error', 'К сожалению, отправить форму не удалось.', true);

        $this->setTemplate('changePasswordOk');
      }
      catch (Exception $e)
      {
        //echo $e->getMessage();
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
        $user = $this->form->getObject();

        $user->is_active = true;
        $user->email = $this->form->getValue('email');
        $user->phonenumber = $this->form->getValue('phonenumber');

        //для правильного сохранения пользователя, берем его region.core_id из сесии и записываем вбазу region_id
        $region = RegionTable::getInstance()->findOneBy('core_id', $this->getUser()->getRegion('id'));
        $user->region_id = ($region instanceof Region) ? $region->id : null;

        $user->setPassword('123456');

        $user = $this->form->save();
        //$user->refresh();
        $this->getUser()->signIn($user);

        if ($request->isXmlHttpRequest())
        {
          return $this->renderJson(array(
            'success' => true,
            'url'     => $signinUrl,
          ));
        }

        return $this->redirect($signinUrl);
      }

      if ($request->isXmlHttpRequest())
      {
        return $this->renderJson(array(
          'success' => false,
          'data' => array(
            'content' => $this->getPartial($this->getModuleName().'/form_register', array('form' => $this->form, 'redirect' => $signinUrl)),
          ),
        ));
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
          'nickname'    => $this->userProfile->getNickname(),
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
