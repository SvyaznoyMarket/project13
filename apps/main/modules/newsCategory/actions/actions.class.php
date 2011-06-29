<?php

/**
 * newsCategory actions.
 *
 * @package    enter
 * @subpackage newsCategory
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsCategoryActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->newsCategoryList = NewsCategoryTable::getInstance()->getList();
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfRequest $request)
  {
    $this->newsCategory = $this->getRoute()->getObject();

    $filter = array(
      'category' => $this->newsCategory,
    );

    $q = NewsTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    $this->newsPager = $this->getPager('News', $q, array(
      'limit' => sfConfig::get('app_news_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->newsPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  public function executeYear(sfRequest $request)
  {
    $this->newsCategory = $this->getRoute()->getObject();

    $filter = array(
      'category' => $this->newsCategory,
      'year'     => $request['year'],
    );

    $q = NewsTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    $this->newsPager = $this->getPager('News', $q, array(
      'limit' => sfConfig::get('app_news_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->newsPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  public function executeMonth(sfRequest $request)
  {
    $this->newsCategory = $this->getRoute()->getObject();

    $filter = array(
      'category' => $this->newsCategory,
      'year'     => $request['year'],
      'month'    => $request['month'],
    );

    $q = NewsTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    $this->newsPager = $this->getPager('News', $q, array(
      'limit' => sfConfig::get('app_news_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->newsPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
}
