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

    private $_validateResult;
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    /*
    $this->productCategoryList = ProductCategoryTable::getInstance()->getList(array(
      'select' => 'productCategory.id, productCategory.name, productCategory.token',
    ));
    */
    $this->productCategoryList = ProductCategoryTable::getInstance()->createQuery()
      ->select('id, name, level, token, token_prefix')
      ->where('is_active = ?', true)
      ->orderBy('root_id, lft')
      ->fetchArray()
    ;

    $this->setVar('infinity', true);

  }
 /**
  * Executes filter action
  *
  * @param sfRequest $request A request object
  */
  public function executeFilter(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();

    $this->productFilter = $this->getProductFilter(array('with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));
    $this->productFilter->bind($request->getParameter($this->productFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery(array(
      'view'       => 'list',
      'with_line'  => 'line' == $request['view'] ? true : false,
      'with_model' => true,
    ));

    $this->productFilter->buildQuery($q);

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    // pager
    /*
    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));
    */
    $this->productPager = $this->getProductPager($q);

    $this->setVar('noInfinity', true);

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

    $q = ProductTable::getInstance()->createBaseQuery(array(
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));
    $q->addWhere('product.type_id = ?', $this->productType->id);
    $this->productFilter->buildQuery($q);

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    // pager
    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
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

    $this->productTagFilter = $this->getProductTagFilter(array('with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));
    $this->productTagFilter->bind($request->getParameter($this->productTagFilter->getName()));

    $q = ProductTable::getInstance()->createBaseQuery(array(
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));
    $this->productTagFilter->buildQuery($q);

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));

    //формируем title
    $title = $this->productCategory->name;
    foreach($this->productTagFilter as $field)
    {
        $val = $field->getValue();
        if (!$val) continue;
        if ($field->getName() == 'price')
        {
            $propStr = $field->renderLabelName();
            if (isset($val['from']))
            {
                $propStr .= ' от ' . $val['from'];
            }
            if (isset($val['to']))
            {
                $propStr .= ' до ' . $val['to'];
            }
            if (isset($val['from']) || isset($val['to']))
            {
                $propStr .= ' рублей';
            }
        }
        else
        {
            $propStr = $field->renderLabelName();
            $valNames = array();
            foreach($val as $valId)
            {
              $info = TagTable::getInstance()->getById($valId);
              $valNames[] = $info['name'];
            }
            $propStr .= ': ' . implode(', ', $valNames);
        }
        $filterList[] = $propStr;
    }
    if (count($filterList)>0) $title .= ' - ' . implode(', ', $filterList);
    $mainCat = $this->productCategory;
    if ($mainCat)
    {
      $rootCat = $mainCat->getRootCategory();
      if ($rootCat->id !== $mainCat->id)
      {
        $title .= ' – '.$rootCat;
      }
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');

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

    $this->productFilter = $this->getProductFilter(array('count' => true, 'with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));
    $this->productTagFilter = $this->getProductTagFilter(array('count' => true, 'with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));

    $q = ProductTable::getInstance()->createBaseQuery(array(
      'view'          => 'list',
      'property_view' => false,
      'with_line'     => false,
      'with_model'    => true,
    ));

    if ($request->hasParameter($this->productFilter->getName()))
    {
      $this->productFilter->bind($request->getParameter($this->productFilter->getName()));
      $this->productFilter->buildQuery($q);
    }
    elseif ($request->hasParameter($this->productTagFilter->getName()))
    {
      $this->productTagFilter->bind($request->getParameter($this->productTagFilter->getName()));
      $this->productTagFilter->buildQuery($q);
    }

    return $this->renderJson(array(
      'success' => true,
      'data'    => $q->count(),
    ));
  }

  private function _seoRedirectOnPageDublicate($request){
    //если передано page=1 или view=compact, отрезаем этот параметр и делаем редирект.
    //необходимо для seo
    $redirectAr = array(
      'page' => 1,
      'view' => 'compact'
    );
    foreach($redirectAr as $key => $val)
    {
        $currentVal = $request->getParameter($key);
        //если требуется редирект с этой страницы
        if (isset($currentVal) && $currentVal == $val)
        {
            $uri = $this->getRequest()->getUri();
            if (strpos($uri, '&') === false)
            {
                $replaceStr = "?$key=$val";
            }
            else
            {
                $replaceStr = array("$key=$val&", "&$key=$val");
            }
            $uri = str_replace($replaceStr, '', $this->getRequest()->getUri());
            $this->redirect( $uri );
        }
    }
  }

  public function executeCategoryAjax(sfWebRequest $request)
  {
    $this->setVar('allOk', false);

    if (!isset($request['productCategory']))
    {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Не указан token категории';
      return $this->_refuse();
    }
    if (!isset($request['page']))
    {
      $request['page'] = 1;
    }
    if (!isset($request['view']))
    {
      $request['view'] = 'compact';
    }

    try
    {
      $this->productCategory = $this->getRoute()->getObject();
    }
    catch(Exception $e) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Категория не найдена';
      return $this->_refuse();
    }

    $this->productFilter = $this->getProductFilter(array('with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));
    $getFilterData = $request->getParameter($this->productFilter->getName()) ;
    $this->productTagFilter = $this->getProductTagFilter(array('with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )), ));
    $getTagFilterData = $request->getParameter($this->productTagFilter->getName());

    if ($this->productCategory->has_line) {
        //если в категории должны отображться линии
        $filter = array(
          'category' => $this->productCategory,
        );

        $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
          'view'      => 'list',
          'with_line' => 'line' == $request['view'] ? true : false,
        ));

        $this->view = 'line';
        $this->list_view = false;

    } elseif ( isset($getFilterData) ) {
        //если установлены фильтры
        $this->productFilter->bind($getFilterData);
        $q = ProductTable::getInstance()->createBaseQuery(array(
          'view'       => 'list',
          'with_line'  => 'line' == $request['view'] ? true : false,
          'with_model' => true,
        ));
        $this->productFilter->buildQuery($q);
        $this->view = $request['view'];

    } elseif ($getTagFilterData) {
        //если установлены тэги
        $this->productTagFilter->bind($getTagFilterData);
        $q = ProductTable::getInstance()->createBaseQuery(array(
          'view'      => 'list',
          'with_line' => 'line' == $request['view'] ? true : false,
        ));
        $this->productTagFilter->buildQuery($q);
        $this->view = $request['view'];
    //если фильтры не установлены
    } else {
        $filter = array(
          'category' => $this->productCategory,
        );
        $q = ProductTable::getInstance()->getQueryByFilter($filter, array(
          'view'      => 'list',//$request['view'],
          'with_line' => 'line' == $request['view'] ? true : false,
        ));
        $this->view = $request['view'];
    }

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    if (isset($request['num'])) $limit = $request['num'];
    else $limit = sfConfig::get('app_product_max_items_on_category', 20);

    /*
    $this->productPager = $this->getPager('Product', $q, $limit, array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));
    */
    $this->productPager = $this->getProductPager($q);

    if($request['page'] > $this->productPager->getLastPage()){
        $this->_validateResult['success'] = false;
        $this->_validateResult['error'] = 'Номер страницы превышает максимальный для списка';
        return $this->_refuse();
    }

    $this->setVar('allOk', true);

  }

  private function _refuse(){
    return $this->renderJson(array(
      'success' => $this->_validateResult['success'],
      'data'    => array(
        'error' => $this->_validateResult['error'],
      ),
    ));
  }


 /**
  * Executes category action
  *
  * @param sfRequest $request A request object
  */
  public function executeCategory(sfWebRequest $request)
  {
    $this->_seoRedirectOnPageDublicate($request);

    try
    {
      $this->productCategory = $this->getRoute()->getObject();
    }
    catch (sfError404Exception $e)
    {
      $this->forward('redirect', 'index');
    }

    // 301-й редирект. Можно удалить 01.02.2012
    if (false === strpos($request['productCategory'], '/'))
    {
      if (!empty($this->productCategory->token_prefix))
      {
        $this->redirect('productCatalog_category', $this->productCategory, 301);
      }
    }

//    $title = $this->productCategory['name'];
//    if ($request->getParameter('page')) {
//      $title .= ' – '.$request->getParameter('page');
//    }
//    $rootCategory = $this->productCategory->getRootCategory();
//    if ($rootCategory->id !== $this->productCategory->id)
//    {
//      $title .= ' – '.$rootCategory;
//    }
//    $this->getResponse()->setTitle($title.' – Enter.ru');

    // SEO ::
    $list = array();
    $ancestorList = $this->productCategory->getNode()->getAncestors();
    if ($ancestorList) foreach ($ancestorList as $ancestor)
    {
        $list[] = (string)$ancestor;
    }

    $list[] = (string)$this->productCategory;
    $title = '%s - интернет-магазин Enter.ru - Москва';
    $this->getResponse()->setTitle(sprintf(
        $title,
        implode(' - ', $list)
    ));
    // :: SEO

    if ($this->productCategory->has_line) //если в категории должны отображться линии
    {
      $this->forward($this->getModuleName(), 'line');
    }

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
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    /*
    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));
    */

    $this->productPager = $this->getProductPager($q);

    // SEO ::
    $list = array();
    $ancestorList = $this->productCategory->getNode()->getAncestors();
    if ($ancestorList) foreach ($ancestorList as $ancestor)
    {
        $list[] = (string)$ancestor;
    }
    $list[] = (string)$this->productCategory;
    $title = '%s - страница %d из %d - интернет-магазин  Enter.ru - '.$this->getUser()->getRegion('name');
    $this->getResponse()->setTitle(sprintf(
      $title,
      implode(' - ', $list),
      $request->getParameter('page', 1),
      $this->productPager->getLastPage()
    ));
    // :: SEO
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
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
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
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);

    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));
  }
 /**
  * Executes product action
  *
  * @param sfRequest $request A request object
  */
  public function executeLine(sfWebRequest $request)
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
      'view'      => 'list',
      'with_line' => 'line' == $request['view'] ? true : false,
    ));

    // sorting
    $this->productSorting = $this->getProductSorting();
    $this->productSorting->setQuery($q);


    /*
    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
    */
    $this->productPager = $this->getProductPager($q);

    $this->view = 'line';
    $this->list_view = false;
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

  public function getProductPager(myDoctrineQuery $q, $limit = null)
  {
	  $page = $this->getRequestParameter('page', 1);
    $limit = $limit ?: sfConfig::get('app_product_max_items_on_category', 20);
    $offset = intval($page - 1) * $limit;
    $this->forward404If($offset < 0, 'Неверный номер страницы');

    $q->offset($offset)->limit($limit);
    $productIds = ProductTable::getInstance()->getIdsByQuery($q);

    $pager = new FilledPager($productIds, $q->countTotal(), $limit);
    $pager->setPage($page);
    $pager->init();
    //$this->forward404If($page > $pager->getLastPage(), 'Номер страницы превышает максимальный для списка');

    return $pager;
  }
}
