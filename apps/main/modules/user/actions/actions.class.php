<?php

/**
 * user actions.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    //пункты для главной страницы личного кабинета
    $list = array(
      array(
          'title' => 'Персональные данные',
          'list' => array(
              array(
                'name'   => 'Личный кабинет',
                'url'    => '@user',
                'routes' => array('user'),
              ),
//              array(
//                'name'   => 'Мои адреса доставки',
//                'url'    => '@userAddress',
//                'routes' => array('@userAddress'),
//              ),
              array(
                'name'   => 'Редактирование профиля',
                'url'    => '@user_edit',
                'routes' => array('user_edit'),
              ),
              array(
                'name'   => 'Изменить пароль',
                'url'    => '@user_changePassword',
                'routes' => array('user_changePassword'),
              ),
          )
      ),
      array(
          'title' => 'Мои заказы',
          'list' => array(
              array(
                'name'   => 'Мои заказы',
                'url'    => '@user_orders',
                'routes' => array('@user_orders', '@user_orders'),
              ),              
            /*  array(
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
              ),*/
          ) 
      )

    );

    /*
    foreach ($list as &$item)
    {
      $routeName =
        false === strpos($item['url'], '?')
        ? false !== strpos($uri, '?') ? strstr($uri, '?', true) : $uri
        : $uri
      ;
      $item['current'] = in_array($routeName, $item['routes']);
    } if (isset($item)) unset($item);
     * */

    $this->setVar('pagesList', $list, true);


  }
 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
    $this->setVar('error', '', true);
      
      /*
    $this->userAddress = $this->getRoute()->getObject();
    //если пользователь  пытается редактировать не свой адрес
    $this->redirectUnless($this->userAddress->user_id == $this->getUser()->getGuardUser()->id, 'userAddress');
*/
    if (!$this->getUser()->isAuthenticated()) $this->redirect('user_signin');
	/**
	 * @todo: убрать рефреш и сделать очистку кэша для пользователя
	 */
	$this->getUser()->getGuardUser()->refresh();
    $this->userProfile = $this->getUser()->getGuardUser()->getData();
    $this->form = new UserForm( $this->getUser()->getGuardUser() );

      //echo 'ok';
    //  exit();
  }


  public function executeUpdate(sfWebRequest $request)
  {
    $this->form = new UserForm($this->getUser()->getGuardUser());

    $data = $request->getParameter($this->form->getName());

    $data['middle_name'] = trim($data['middle_name']);
    $data['last_name'] = trim($data['last_name']);
    $data['occupation'] = trim($data['occupation']);
    $data['skype'] = trim($data['skype']);
    
    $this->form->bind( $data );
    $this->setVar('error', '', true);

    if ($this->form->isValid())
    {
      try
      {
            $this->form->save();
            $this->redirect('user_edit');
      }
      catch (Exception $e)
      {
            #echo $e->getMessage();
            $this->setVar('error', 'К сожалению, данные сохранить не удалось.', true);          
            $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    } else {
		//echo $this->form->renderGlobalErrors();
	}

    $this->userProfile = $this->getUser()->getGuardUser()->getData();

    $this->setTemplate('edit');
  }

  public function executeOrders(sfWebRequest $request)
  {
  }
}
