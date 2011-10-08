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
  * Executes slot component
  *
  * @param string $token Токен слота
  */
  public function executeSlot()
  {
    $this->slot = $this->token ? SlotTable::getInstance()->getByToken($this->token) : false;
    if (!$this->slot)
    {
      return sfView::NONE;
    }
  }
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
 /**
  * Executes pagination component
  *
  * @param myDoctrinePager $pager Листалка товаров
  */
  public function executePagination()
  {
    $list = array();
    foreach ($this->pager->getLinks() as $page)
    {
      $list[] = $page;
    }

    $this->first = $this->pager->getFirstPage();
    $this->last = $this->pager->getLastPage();
    $this->page = $this->pager->getPage();

    $this->setVar('list', $list, true);
  }
}
