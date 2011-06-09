<?php

/**
 * default components.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultComponents extends myComponents
{
/**
  * Executes navigation component
  *
  * @param array $list Список элементов навигации
  */
  public function executeNavigation()
  {
    if (empty($this->list))
    {
      return sfView::NONE;
    }
  }
}
