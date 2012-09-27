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
            'name'   => 'Адвокат клиента',
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

    $guardUser = $this->getUser()->getGuardUser();
    $this->form = $this->getUserForm();
  }


  public function executeUpdate(sfWebRequest $request)
  {
    $this->form = $this->getUserForm();

    $this->form->bind($request[$this->form->getName()]);
    $this->setVar('error', '', true);


    if ($this->form->isValid())
    {
      try
      {
        $data = $this->form->getValues();
        $r = CoreClient::getInstance()->query('user/update', array('token' => $this->getUser()->getGuardUser()->getToken()), array(
          'first_name'  => $data['first_name'],
          'middle_name' => $data['middle_name'],
          'last_name'   => $data['last_name'],
          'sex'         => $data['gender'],
          'email'       => $data['email'],
          'modile'      => $data['phonenumber'],
          'phone'       => $data['phonenumber_city'],
          'skype'       => $data['skype'],
          'birthday'    => $data['birthday'],
          'occupation'  => $data['occupation'],
        ));

        $this->getUser()->setFlash('message', 'Данные успешно обновлены.');
        $this->redirect('user_edit');
      }
      catch (Exception $e)
      {
        #echo $e->getMessage();
        $this->setVar('error', 'К сожалению, данные сохранить не удалось.', true);
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->setTemplate('edit');
  }

  public function executeOrders(sfWebRequest $request)
  {
  }

  public function executeLegalConsultation(sfWebRequest $request){
    $guardUser = $this->getUser()->getGuardUser();

    $callback = new Callback();
    $callback->setEmail($guardUser->getEmail());
    $callback->setName($guardUser->getFullName());

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

  private function getUserForm() {
    $guardUser = $this->getUser()->getGuardUser();

    $form = new UserForm();
    $form->setDefaults(array(
      'first_name'       => $guardUser->getFirstName(),
      'middle_name'      => $guardUser->getMiddleName(),
      'last_name'        => $guardUser->getLastName(),
      'gender'           => $guardUser->getGender(),
      'email'            => $guardUser->getEmail(),
      'phonenumber'      => $guardUser->getPhonenumber(),
      'phonenumber_city' => $guardUser->getHomePhonenumber(),
      'skype'            => $guardUser->getSkype(),
      'birthday'         => $guardUser->getBirthday(),
      'occupation'       => $guardUser->getOccupation(),
    ));

    return $form;
  }

}
