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
                'routes' => array('@user'),
              ),
//              array(
//                'name'   => 'Адрес доставки',
//                'url'    => '@userAddress',
//                'routes' => array('@userAddress'),
//              ),
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
          )
      ),
      array(
          'title' => 'Мои товары',
          'list' => array(
              array(
                'name'   => 'Корзина товаров',
                'url'    => '@cart',
                'routes' => array('@cart'),
              ),
              array(
                'name'   => 'Заказы',
                'url'    => '@order',
                'routes' => array('@order', '@order_show'),
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

    $a = $request->getParameter($this->form->getName());
    //$a['id'] = 2;
    $this->form->bind( $a );
    
    if ($this->form->isValid())
    {
      try
      {
	   $this->form->save();
       //$coreResult =  Core::getInstance()->query('user.update',array('id'=>$this->getUser()->getGuardUser()->core_id),$this->form->getValues());
       //if ($coreResult['confirmed']==1){
//            $user = UserTable::getInstance()->findOneById($this->getUser()->getGuardUser()->id);
//            $user->setCorePush(false);
//            $user->fromArray( $this->form->getValues() );
//            $user->save();
           
			//$this->form->save();
       //}
        $this->redirect('user_edit');
      }
      catch (Exception $e)
      {
		  echo $e->getMessage();
//		  echo $e->getTraceAsString();
//		  exit();
         // exit();
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    } else {
		//echo $this->form->renderGlobalErrors();
	}

    $this->userProfile = $this->getUser()->getGuardUser()->getData();

    $this->setTemplate('edit');      
  }  
  
  
}
