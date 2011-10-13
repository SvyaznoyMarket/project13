<?php

/**
 * guard components.
 *
 * @package    enter
 * @subpackage guard
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class guardComponents extends myComponents
{
 /**
  * Executes form_auth component
  *
  * @param UserFormSignin $formSignin Форма авторизации
  * @param UserFormRegister $formRegister Форма регистрации
  */
  public function executeForm_auth()
  {
    if (!($this->formSignin instanceof UserFormSignin))
    {
      $this->formSignin = new UserFormSignin();
    }

    if (!($this->formRegister instanceof UserFormRegister))
    {
      $this->formRegister = new UserFormRegister();
    }
  }
 /**
  * Executes oauth_links component
  *
  * @param UserFormSignin $form Форма авторизации
  */
  public function executeOauth_links()
  {
    require_once(dirname(__FILE__).'/../lib/BaseOpenAuthProvider.class.php');

    $list = array();

    foreach (sfConfig::get('app_open_auth_provider') as $name => $config)
    {
      require_once(dirname(__FILE__).'/../lib/OpenAuth'.sfInflector::camelize($name).'Provider.class.php');

      $class = sfInflector::camelize('open_auth_'.$name.'_provider');
      $provider = new $class($config);

      $list[$name] = array(
        'token' => $name,
        'name'  => $config['title'],
        'url'   => url_for('user_signin', array('provider' => $name)),
      );
    }

    $this->setVar('list', $list, true);
  }
}
