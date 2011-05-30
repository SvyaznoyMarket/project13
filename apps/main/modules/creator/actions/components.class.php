<?php

/**
 * creator components.
 *
 * @package    enter
 * @subpackage creator
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class creatorComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param Doctrine_Collection $creatorList Коллекция производителей
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->creatorList as $creator)
    {
      $list[] = array(
        'name'     => (string)$creator,
        'creator'  => $creator,
      );
    }
    
    $this->setVar('list', $list, true);
  }
}
