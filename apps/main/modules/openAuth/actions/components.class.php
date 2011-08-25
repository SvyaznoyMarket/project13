<?php

/**
 * openAuth components.
 *
 * @package    enter
 * @subpackage openAuth
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class openAuthComponents extends myComponents
{
 /**
  * Executes form component
  *
  * @param string $view Вид формы
  */
  public function executeForm()
  {
  }
 /**
  * Executes show component
  *
  */
  public function executeShow()
  {
    require_once(dirname(__FILE__).'/../lib/BaseOpenAuthProvider.class.php');
    require_once(dirname(__FILE__).'/../lib/OpenAuthVkontakteProvider.class.php');

    $list = array();

    foreach (sfConfig::get('app_open_auth_provider') as $name => $config)
    {
      $class = sfInflector::camelize('open_auth_'.$name.'_provider');
      $provider = new $class($config);

      $list[$name] = array(
        'token' => $name,
        'name'  => $config['title'],
        'url'   => url_for('openAuth_signin', array('provider' => $name)),
        'data'  => $provider->getData(),
      );
    }

    $this->setVar('list', $list, true);
  }
}

