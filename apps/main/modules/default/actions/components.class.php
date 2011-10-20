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

    $list = (array)$this->list;
    array_unshift($list, array(
      'name' => 'Enter.ru',
      'url'  => url_for('@homepage'),
    ));

    $this->setVar('list', $list, true);
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
 /**
  * Executes footer component
  *
  * @param string $view Вид
  */
  public function executeFooter()
  {
    if (!in_array($this->view, array('default', 'main', 'compact')))
    {
      $this->view = 'default';
    }
  }
}
