<?php

/**
 * userAddress actions.
 *
 * @package    enter
 * @subpackage userAddress
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userAddressActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->userAddressList = $this->getUser()->getGuardUser()->getAddressList();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new UserAddressForm();

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $this->form->getObject()->user_id = $this->getUser()->getGuardUser()->id;
        $this->form->save();
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->redirect('userAddress');
  }

  public function executeUpdate(sfRequest $request)
  {
    $userAddress = $this->getRoute()->getObject();

    //если пользователь  пытается сохранить не свой адрес
    $this->redirectIf($userAddress->user_id != $this->getUser()->getGuardUser()->id, 'userAddress');
    $this->form = new UserAddressForm($userAddress);

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $this->form->save();
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->redirect('userAddress');

  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->userAddress = $this->getRoute()->getObject();
    $this->userAddressList = $this->getUser()->getGuardUser()->getAddressList();

    //если пользователь  пытается редактировать не свой адрес
    $this->redirectIf($this->userAddress->user_id != $this->getUser()->getGuardUser()->id, 'userAddress');

  }

  public function executeDelete(sfWebRequest $request)
  {
    $userAddress = $this->getRoute()->getObject();

    UserAddressTable::getInstance()->createQuery()
      ->delete()
      ->where('id = ?', $userAddress->id)
      ->execute()
    ;

    $this->redirect($this->getRequest()->getReferer());
  }
}
