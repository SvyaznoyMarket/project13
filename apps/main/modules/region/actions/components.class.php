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

  public function executeSelect()
  {
    $num_row = 4;
    $this->regions = RepositoryManager::getRegion()->getShowInMenu();

    $this->columns_count = array();
    $count = count($this->regions);

    for ($i = 0; $i < $num_row; $i++)
    {
      $this->columns_count[$i] = (int)floor($count / $num_row) + (($count % $num_row) > $i ? 1 : 0);
    }
  }

}
