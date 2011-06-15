<?php
/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsComponents extends myComponents
{
 /**
  * Executes pagination component
  *
  * @param myDoctrinePager $newsPager Листалка новостей
  */
  public function executePagination()
  {
    if (!$this->newsPager->haveToPaginate())
    {
      return sfView::NONE;
    }
  }

 /**
  * Executes pager component
  *
  * @param myDoctrinePager $newsPager Листалка новостей
  */
  public function executePager()
  {
    $this->setVar('newsList', $this->newsPager->getResults(), true);
  }

 /**
  * Executes list component
  *
  * @param myDoctrineCollection $productList Список товаров
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->newsList as $news)
    {
      $list[] = array(
        'name'  => (string)$news,
        'news'  => $news,
      );
    }

    $this->setVar('list', $list, true);
  }

  public function executeFilter()
  {
    $categories = NewsCategoryTable::getInstance()->getList();

    $filter = array();
    foreach ($categories as $category)
    {
      $filter[$category->token]['category'] = $category;
      $months = NewsTable::getInstance()->getMonthsByCategory($category);
      foreach ($months as $month)
      {
        if (!isset($filter[$category->token]['year'][$month['y']]))
        {
          $filter[$category->token]['year'][$month['y']] = array('months' => array(), 'count' => 0, );
        }
        $filter[$category->token]['year'][$month['y']]['months'][$month['m']] = $month['c'];
        $filter[$category->token]['year'][$month['y']]['count'] += $month['c'];
      }
    }
    $this->setVar('filter', $filter, true);
  }
}