<?php

/**
 * page components.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class regionComponents extends myComponents
{

  public function executeTop_list()
  {
    $this->regions = RepositoryManager::getRegion()->getShopAvailable();
  }
}
