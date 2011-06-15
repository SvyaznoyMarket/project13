<?php

/**
 * userTag components.
 *
 * @package    enter
 * @subpackage userTag
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userTagComponents extends myComponents
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
    foreach ($this->userTagList as $userTag)
    {
      $list[] = array(
        'name'    => (string)$product,
        'userTag' => $userTag,
      );
    }

    $this->setVar('list', $list, true);
  }
}
