<?php

/**
 * user components.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userComponents extends myComponents
{
 /**
  * Executes profile component
  *
  */
  public function executeProfile()
  {
    if (!$this->getUser()->isAuthenticated())
    {
      return sfView::NONE;
    }

    $this->view = $this->getUser()->getType();
  }
 /**
  * Executes profile_client component
  *
  */
  public function executeProfile_client()
  {
  }
 /**
  * Executes profile_partner component
  *
  */
  public function executeProfile_partner()
  {
  }
 /**
  * Executes menu component
  *
  */
  public function executeMenu()
  {
    if ('frame' == $this->getLayout())
    {
      return sfView::NONE;
    }

    $uri = $this->getContext()->getRouting()->getCurrentInternalUri(true);

    //пункты для левого меню, отображаемого на всех страницах ЛК
    $list = array(

          array(
            'name'   => 'Личный кабинет',
            'url'    => '@user',
            'routes' => array('@user'),
          ),
          array(
            'name'   => 'Редактирование профиля',
            'url'    => '@user_edit',
            'routes' => array('@user_edit'),
          ),            
          array(
            'name'   => 'Пароль',
            'url'    => '@user_changePassword',
            'routes' => array('@user_changePassword'),
          ),            
          array(
            'name'   => 'Адрес доставки',
            'url'    => '@userAddress',
            'routes' => array('@userAddress'),
          ),        
    );

    foreach ($list as &$item)
    {
      $routeName =
        false === strpos($item['url'], '?')
        ? false !== strpos($uri, '?') ? strstr($uri, '?', true) : $uri
        : $uri
      ;
      $item['current'] = in_array($routeName, $item['routes']);
    } if (isset($item)) unset($item);

    $this->setVar('leftMenu', $list, true);
  }
  
  public function executeShortuserinfo()
  {
      $this->setVar('user', $this->getUser(), true);
      $this->setVar('userData', $this->getUser()->getGuardUser(), true);      
  }  
  
  public function executeNavigation()
  {
    //    $this->setVar('list', $list, true);
  }  
}
