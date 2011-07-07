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
    $uri = $this->getContext()->getRouting()->getCurrentInternalUri(true);

    $list = array(
      array(
        'name'   => 'Личный кабинет',
        'url'    => '@user',
        'routes' => array('@user'),
      ),
      array(
        'name'   => 'Корзина товаров',
        'url'    => '@cart',
        'routes' => array('@cart'),
      ),
      array(
        'name'   => 'История просмотра товаров',
        'url'    => '@userProductHistory',
        'routes' => array('@userProductHistory'),
      ),
      array(
        'name'   => 'Отложенные товары',
        'url'    => '@userDelayedProduct',
        'routes' => array('@userDelayedProduct'),
      ),
      array(
        'name'   => 'Сравнение товаров',
        'url'    => '@userProductCompare',
        'routes' => array('@userProductCompare', '@userProductCompare_show'),
      ),
      array(
        'name'   => 'Метки товаров',
        'url'    => '@userTag',
        'routes' => array('@userTag'),
      ),
      array(
        'name'   => 'Пароль',
        'url'    => '@user_changePassword',
        'routes' => array('@user_changePassword'),
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

    $this->setVar('list', $list, true);
  }
}
