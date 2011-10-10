<?php

/**
 * productCatalog actions.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCatalogActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->productCategoryList = ProductCategoryTable::getInstance()->getList(array(
      'select' => 'productCategory.id, productCategory.name, productCategory.token',
    ));
  }
 /**
  * Executes filter action
  *
  * @param sfRequest $request A request object
  */
  public function executeFilter(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();

    $this->productFilter = $this->getProductFilter();
    $this->productFilter->bind($request->getParameter($this->productFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery();
    $this->productFilter->buildQuery($q);

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
 /**
  * Executes count action
  *
  * @param sfRequest $request A request object
  */
  public function executeCount(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();

    $this->productFilter = $this->getProductFilter();
    $this->productFilter->bind($request->getParameter($this->productFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery();
    $this->productFilter->buildQuery($q);


    return $this->renderJson(array(
      'success' => true,
      'data'    => $q->count(),
    ));
  }
 /**
  * Executes category action
  *
  * @param sfRequest $request A request object
  */
  public function executeCategory(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    
    if (!$this->productCategory->getNode()->hasChildren())
    {
      $this->forward($this->getModuleName(), 'product');
    }
  }
 /**
  * Executes product action
  *
  * @param sfRequest $request A request object
  */
  public function executeProduct(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    
    $filter = array(
      'category' => $this->productCategory,
    );

    $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);


    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
 /**
  * Executes creator action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreator(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    $this->creator = $this->getRoute()->getCreatorObject();

    $filter = array(
      'category' => $this->productCategory,
      'creator'  => $this->creator,
    );

    $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
  }
 /**
  * Executes special action
  *
  * @param sfRequest $request A request object
  */
  public function executeSpecial(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    $this->creator = $this->getRoute()->getCreatorObject();

    $filter = array(
      'category' => $this->productCategory,
      'creator'  => $this->creator,
    );

    $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
      'view'  => 'list',
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
  }



  protected function getProductFilter()
  {
    return new myProductFormFilter(array(), array(
      'productCategory' => $this->productCategory,
    ));
  }

  protected function getProductSorting()
  {
    $sorting = new myProductSorting();

    $active = array_pad(explode('-', $this->getRequest()->getParameter('sort')), 2, null);
    $sorting->setActive($active[0], $active[1]);

    return $sorting;
  }
}
