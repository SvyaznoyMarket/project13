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
  * @param myDoctrineCollection $userTagList Коллекция пользовательских тегов
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
  * @param UserTagForm $form Форма пользовательских тегов
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

    $userTagList = $this->product->getUserTagList(array('user_id' => $user_id));
    $userTagList->indexBy('id');

    $list = array();
    foreach (UserTagTable::getInstance()->getListByUser($user_id) as $userTag)
    {
      $list[] = array(
        'name'  => (string)$userTag,
        'class' => $userTagList->getByIndex('id', $userTag->id) ? 'delete' : 'add',
        'url'   => url_for(array('sf_route' => $userTagList->getByIndex('id', $userTag->id) ? 'userTag_unlinkProduct' : 'userTag_linkProduct', 'sf_subject' => $userTag, 'product' => $this->product->token)),
      );
    }

    $this->setVar('list', $list, true);
  }
}
