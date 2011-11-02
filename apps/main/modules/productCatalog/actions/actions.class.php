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

    // pager
    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
 /**
  * Executes productType action
  *
  * @param sfRequest $request A request object
  */
  public function executeProductType(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    $this->productType = !empty($request['productType']) ? ProductTypeTable::getInstance()->getById($request['productType']) : false;
    $this->forward404Unless($this->productType);

    $this->productFilter = $this->getProductFilter(array(
      'productType' => $this->productType,
    ));
    $this->productFilter->bind($request->getParameter($this->productFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery();
    $q->addWhere('product.type_id = ?', $this->productType->id);
    $this->productFilter->buildQuery($q);

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    // pager
    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
 /**
  * Executes tag action
  *
  * @param sfRequest $request A request object
  */
  public function executeTag(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();

    $this->productTagFilter = $this->getProductTagFilter();
    $this->productTagFilter->bind($request->getParameter($this->productTagFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery();
    $this->productTagFilter->buildQuery($q);

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

    $this->productFilter = $this->getProductFilter(array('count' => true, ));
    $this->productTagFilter = $this->getProductTagFilter(array('count' => true, ));

    if ($request->hasParameter($this->productFilter->getName()))
    {
      $this->productFilter->bind($request->getParameter($this->productFilter->getName()));

      $q = ProductTable::getInstance()->createBaseQuery();
      $this->productFilter->buildQuery($q);
    }
    elseif ($request->hasParameter($this->productTagFilter->getName()))
    {
      $this->productTagFilter->bind($request->getParameter($this->productTagFilter->getName()));

      $q = ProductTable::getInstance()->createBaseQuery();
      $this->productTagFilter->buildQuery($q);
    }

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

    $title = $this->productCategory['name'];
    if ($request->getParameter('page')) {
      $title .= ' – '.$request->getParameter('page');
    }
    $rootCategory = $this->productCategory->getRootCategory();
    if ($rootCategory->id !== $this->productCategory->id)
    {
      $title .= ' – '.$rootCategory;
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');

    if (false
      || !$this->productCategory->getNode()->hasChildren()                  //нет дочерних категорий
      //|| (1 == $this->productCategory->getNode()->getChildren()->count()) // одна дочерняя категория
    ) {
      $this->forward($this->getModuleName(), 'product');
    }

    // если категория корневая
    if ($this->productCategory->getNode()->isRoot())
    {
      $this->setTemplate('categoryRoot');
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

	$title = $this->productCategory['name'];
	if ($request->getParameter('page'))
  {
		$title .= ' – '.$request->getParameter('page');
	}
	$rootCategory = $this->productCategory->getRootCategory();
	if ($rootCategory->id !== $this->productCategory->id)
  {
		$title .= ' – '.$rootCategory;
	}
	$this->getResponse()->setTitle($title.' – Enter.ru');

    $filter = array(
      'category' => $this->productCategory,
    );

    $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
      'view'            => 'list',
      'with_properties' => 'expanded' == $request['view'],
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



  protected function getProductFilter($params = array())
  {
    return new myProductFormFilter(array(), myToolkit::arrayDeepMerge(array(
      'productCategory' => $this->productCategory,
    ), $params));
  }

  protected function getProductTagFilter($params = array())
  {
    return new myProductTagFormFilter(array(),  myToolkit::arrayDeepMerge(array(
      'productCategory' => $this->productCategory,
    ), $params));
  }

  protected function getProductSorting()
  {
    $sorting = new myProductSorting();

    $active = array_pad(explode('-', $this->getRequest()->getParameter('sort')), 2, null);
    $sorting->setActive($active[0], $active[1]);

    return $sorting;
  }
}
