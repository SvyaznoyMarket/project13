<?php

/**
 * user actions.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

/**
 * @property $form CallbackForm
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
          'title' => 'Моя персональная информация',
          'list' => array(
              /*
              array(
                'name'   => 'Личный кабинет',
                'url'    => '@user',
                'routes' => array('user'),
              ),
               */
//              array(
//                'name'   => 'Мои адреса доставки',
//                'url'    => '@userAddress',
//                'routes' => array('@userAddress'),
//              ),
              array(
                'name'   => 'Изменить мои данные',
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
          'title' => 'Мои товары',
          'list' => array(
              array(
                'name'   => 'Мои заказы',
                'num'    => RepositoryManager::getOrder()->countByUserToken($this->getUser()->getGuardUser()->getToken()),
                'url'    => '@user_orders',
                'routes' => array('@user_orders', '@user_orders'),
              ),
            /*  array(
                'name'   => 'Корзина товаров',
                'url'    => '@cart',
                'routes' => array('@cart'),
              ),*/
          )
      ),

      array(
        'title' => 'cEnter защиты прав потребителей ',
        'list' => array(
          array(
            'name'   => 'Юридическая помощь',
            'url'    => '@user_legalConsultation',
            'routes' => array('user_legalConsultation'),
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
	  //$this->getUser()->getGuardUser()->refresh();
    //$this->userProfile = $this->getUser()->getGuardUser()->getData();
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

  public function executeLegalConsultation(sfWebRequest $request){
    $userData = $this->getUser()->getGuardUser()->getData();

    $email = (isset($userData['email']) && strlen($userData['email']) > 1)? $userData['email'] : '';
    $name = (isset($userData['last_name']) && strlen($userData['last_name']) > 1)? $userData['last_name'].' ' : '';
    $name .= $userData['first_name'];
    $name .= (isset($userData['middle_name']) && strlen($userData['middle_name']) > 1)? ' '.$userData['middle_name'] : '';

    $callback = new Callback();
    $callback->setEmail($email);
    $callback->setName($name);

    $this->form = new CallbackForm($callback);
  }

  public function executeSendLegalConsultation(sfWebRequest $request){
    $this->form = new CallbackForm();
    $data = $request->getParameter($this->form->getName());
    $data['channel_id'] = 2;
    $this->form->bind($data);
    $this->setVar('error', '', true);

    if ($this->form->isValid())
    {
      try
      {
        #$this->form->getObject()->setCorePush(false);
        $this->form->save();
        $this->setTemplate('sendLegalConsultationOk');
      }
      catch (Exception $e)
      {
        //echo $e->getMessage();
        $this->setVar('error', 'К сожалению, отправить форму не удалось.', true);
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
        $this->setTemplate('legalConsultation');
      }
    } else {
      //echo $this->form->renderGlobalErrors();
      //$this->redirect('callback');
      $this->setTemplate('legalConsultation');
    }
  }

}
