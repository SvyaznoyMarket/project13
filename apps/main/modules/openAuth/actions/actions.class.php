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
    $this->forward404Unless($request->isMethod('post'));

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

    $this->forward($this->getModuleName(), sfInflector::camelize('signin_'.$providerName));
  }
 /**
  * Executes signinVkontakte action
  *
  * @param sfRequest $request A request object
  */
  public function executeSigninVkontakte(sfWebRequest $request)
  {
    $result = array();

    $providerName = $request['provider'];
    $provider = $this->getProvider($providerName);

    if ($userProfile = $provider->getProfile($request))
    {
      if ($userProfile->exists())
      {
        $this->getUser()->signin($userProfile->User);

        $result = array(
          'success' => true,
          'data'    => array(
            'action' => 'signin',
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



  protected function getProvider($name)
  {
    $class = sfInflector::camelize('open_auth_'.$name.'_provider');
    $this->forward404Unless(!empty($name) && class_exists($class));

    $providers = sfConfig::get('app_open_auth_provider');

    return new $class($providers[$name]);
  }
}
