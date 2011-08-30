<?php

/**
 * openAuth actions.
 *
 * @package    enter
 * @subpackage openAuth
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class openAuthActions extends myActions
{
 /**
  * Executes signin action
  *
  * @param sfRequest $request A request object
  */
  public function executeSignin(sfWebRequest $request)
  {
    $providerName = $request['provider'];

    if ($this->getUser()->isAuthenticated())
    {
      return $this->renderJson(array(
        'success' => false,
        'message' => array(
          'name' => 'Вы уже авторизованы',
        ),
      ));
    }

    $this->forward($this->getModuleName(), 'signin'.sfInflector::camelize($providerName));
  }
 /**
  * Executes signinVkontakte action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninVkontakte(sfWebRequest $request)
  {
    $result = array();
    $provider = $this->getProvider();

    if ($userProfile = $provider->getProfile($request))
    {
      if ($userProfile->exists())
      {
        $this->getUser()->signin($userProfile->User);

        $result = array(
          'success' => true,
          'data'    => array(
            'action' => 'reload',
            'param'  => array(
              'url'    => $this->generateUrl('user'),
            ),
          ),
        );
      }
      else {
        $this->getUser()->setProfile($userProfile);
        $result = array(
          'success' => true,
          'data'    => array(
            'action' => 'quickRegister',
            'param'  => array(
              'url'    => $this->generateUrl('user_quickRegister'),
            ),
          ),
        );
      }
    }
    else {
      $result = array(
        'success' => false,
      );
    }

    return $this->renderJson($result);
  }
 /**
  * Executes signinFacebook action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninFacebook(sfWebRequest $request)
  {
    $result = array();
    $provider = $this->getProvider();

    if ($userProfile = $provider->getProfile($request))
    {
      if ($userProfile->exists())
      {
        $this->getUser()->signin($userProfile->User);

        $result = array(
          'success' => true,
          'data'    => array(
            'action' => 'reload',
            'param'  => array(
              'url'    => $this->generateUrl('user'),
            ),
          ),
        );
      }
      else {
        $this->getUser()->setProfile($userProfile);
        $result = array(
          'success' => true,
          'data'    => array(
            'action' => 'quickRegister',
            'param'  => array(
              'url'    => $this->generateUrl('user_quickRegister'),
            ),
          ),
        );
      }
    }
    else {
      $result = array(
        'success' => false,
      );
    }

    return $this->renderJson($result);
  }
 /**
  * Executes signinTwitter action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninTwitter(sfWebRequest $request)
  {
    $user = $this->getUser();
    $result = array();
    $provider = $this->getProvider();

    if ($request->hasParameter('denied'))
    {
      $this->setTemplate('signin');

      return sfView::ERROR;
    }
    else if ($request->hasParameter('oauth_token'))
    {
      if ((!$user->getAttribute('twitter_oauth_access_token')) && (!$user->getAttribute('twitter_oauth_access_token_secret')))
      {
        $token = $provider->getAccessToken($user->getAttribute('twitter_oauth_request_token'), $user->getAttribute('twitter_oauth_request_token_secret'));

        $user->setAttribute('twitter_oauth_access_token', $token['oauth_token']);
        $user->setAttribute('twitter_oauth_access_token_secret', $token['oauth_token_secret']);
      }

      $userProfile = $provider->getUserProfile(
        $user->getAttribute('twitter_oauth_access_token'),
        $user->getAttribute('twitter_oauth_access_token_secret')
      );

      if ($userProfile)
      {
        if ($userProfile->exists())
        {
          $this->getUser()->signin($userProfile->User);
        }
        else {
          $this->getUser()->setProfile($userProfile);

          $this->redirect('user_quickRegister');
        }

        $this->redirect('user');
      }
      else {
        $user->setAttribute('twitter_oauth_request_token', null);
        $user->setAttribute('twitter_request_token_secret', null);
        $user->setAttribute('twitter_oauth_state', null);
        $user->setAttribute('twitter_oauth_access_token', null);
        $user->setAttribute('twitter_oauth_access_token_secret', null);

        $this->redirect('user_signin');
      }
    }
    else if ($request->isXmlHttpRequest())
    {
      $token = $provider->getRequestToken();
      $user->setAttribute('twitter_oauth_request_token', $token['oauth_token']);
      $user->setAttribute('twitter_oauth_request_token_secret', $token['oauth_token_secret']);

      return $this->renderJson(array(
        'success' => true,
        'data'    => array(
          'url'    => $provider->getSigninUrl($token),
        ),
      ));
    }

    $this->redirect('user_signin');
  }
 /**
  * Executes signinMailru action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninMailru(sfWebRequest $request)
  {
    $result = array();
    $provider = $this->getProvider();

    if ($request->hasParameter('error'))
    {
      $this->setTemplate('signin');

      return sfView::ERROR;
    }
    else if ($userProfile = $provider->getProfile($request))
    {
      if ($userProfile->exists())
      {
        $this->getUser()->signin($userProfile->User);

        $this->redirect('user');
      }
      else {
        $this->getUser()->setProfile($userProfile);

        $this->redirect('user_quickRegister');
      }
    }

    $this->redirect('user_signin');
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
