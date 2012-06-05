<?php

/**
 * userTag components.
 *
 * @package    enter
 * @subpackage userTag
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userAddressComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $userAddressList Коллекция адресов пользователя
  *
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->userAddressList as $userAddress)
    {
      $list[] = array(
        'name'    => (string)$userAddress,
        'userAddress' => $userAddress,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes form component
  *
  * @param UserAddressForm $form Форма адресов пользователя
  *
  */
  public function executeForm()
  {
    if (!isset($this->form))
    {
      $this->form = new UserAddressForm();
    }
  }
 /**
  * Executes product link component
  *
  * @param Product $product Товар
  *
  */
  public function executeProduct_link()
  {
    $user_id = $this->getUser()->isAuthenticated() ? $this->getUser()->getGuardUser()->id : false;
    if (!$user_id)
    {
      return sfView::NONE;
    }

    $this->setVar('list', array(), true);
  }
}
