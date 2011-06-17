<?php

/**
 * productHelper actions.
 *
 * @package    enter
 * @subpackage productHelper
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productHelperActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->productHelperList = ProductHelperTable::getInstance()->getList();
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->productHelper = $this->getRoute()->getObject();
  }
 /**
  * Executes answer action
  *
  * @param sfRequest $request A request object
  */
  public function executeAnswer(sfWebRequest $request)
  {
    $this->productHelper = $this->getRoute()->getObject();
  }
 /**
  * Executes result action
  *
  * @param sfRequest $request A request object
  */
  public function executeResult(sfWebRequest $request)
  {
    $this->productHelper = $this->getRoute()->getObject();

    $this->productHelperFilter = $this->getProductHelperFilter();
    $this->productHelperFilter->bind($request->getParameter($this->productHelperFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery();
    $this->productHelperFilter->buildQuery($q);

    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  protected function getProductHelperFilter()
  {
    return new myProductHelperFormFilter(array(), array(
      'productHelper' => $this->productHelper,
    ));
  }
}
