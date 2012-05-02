<?php

/**
 * productCatalog_ actions.
 *
 * @package    enter
 * @subpackage productCatalog_
 * @author     Связной Маркет
 *
 * @property ProductCorePagerContainer $productPager
 * @method myUser getUser
 * @method sfWebResponse getResponse
 * @method sfWebRequest getRequest
 */
class productCatalog_Actions extends myActions
{
  public function preExecute()
  {
    parent::preExecute();
    $this->getRequest()->setParameter('_template', 'product_catalog');
  }

  /**
   * @todo rewrite to core api
   */
  public function executeIndex()
  {
    $productCategoryList = ProductCategoryTable::getInstance()->createQuery()
      ->select('id, name, level, token, token_prefix')
      ->where('is_active = ?', true)
      ->orderBy('root_id, lft')
      ->fetchArray();

    $this->setVar('productCategoryList', $productCategoryList);

    $list = array();
    foreach ($productCategoryList as $productCategory)
    {
      $list[] = array(
        'name' => $productCategory['name'],
        'url' => $this->generateUrl('productCatalog_category', array('productCategory' => $productCategory['token_prefix'] ? ($productCategory['token_prefix'] . '/' . $productCategory['token']) : $productCategory['token'])),
        'level' => $productCategory['level'],
      );
    }

    $this->setVar('list', $list, true);
    $this->setVar('infinity', true);
  }

  public function executeProductType(sfWebRequest $request)
  {
    $productType = !empty($request['productType']) ? ProductTypeTable::getInstance()->getById($request['productType']) : false;
    $this->forward404Unless($productType);
    $this->loadList($request);
  }

  public function executeProduct(sfWebRequest $request)
  {
    $productCategory = $this->getProductCategory($request);

    $this->loadList($request);

    // SEO ::
    $list = array();
    $ancestorList = ProductCategoryTable::getInstance()->getAncestorList($productCategory, array(
      'hydrate_array' => true,
      'select' => 'productCategory.id, productCategory.name',
    ));
    foreach ($ancestorList as $ancestor)
    {
      $list[] = $ancestor['name'];
    }
    $list[] = $productCategory->name;
    $title = '%s - страница %d из %d - интернет-магазин  Enter.ru - ' . $this->getUser()->getRegion('name');
    $this->getResponse()->setTitle(sprintf(
      $title,
      implode(' - ', $list),
      $request->getParameter('page', 1),
      $this->productPager->getLastPage()
    ));
  }

  public function executeCategory(sfWebRequest $request)
  {
    if (!$request->isXmlHttpRequest())
      $this->seoRedirectOnPageDublicate($request);

    $productCategory = $this->getProductCategory($request);

    if ($productCategory->has_line) // если в категории должны отображться линии
    {
      $this->forward($this->getModuleName(), 'line');
    }

    if (!$productCategory->hasChildren()) // нет дочерних категорий
    {
      $this->forward($this->getModuleName(), 'product');
    }

    if($request->getParameter(ProductCoreFormFilterSimple::NAME))
    {
      $this->forward($this->getModuleName(), 'categoryTag');
    }

    // если категория корневая
    if ($productCategory->isRoot())
    {
      $this->forward($this->getModuleName(), 'categoryRoot');
    }

    $this->forward($this->getModuleName(), 'categoryTag');
  }

  public function executeCategoryRoot(sfWebRequest $request)
  {
    $productCategory = $this->getProductCategory($request);
    $categoryTree = RepositoryManager::getProductCategory()->getTree(
      $productCategory->core_id,
      $productCategory->level + 2,
      false
    );
    $productFilter = $this->getProductFilter($request);
    /** @var $rootCategory ProductCategoryEntity */
    $rootCategory = reset($categoryTree);
    /** @var $categoryList ProductCategoryEntity[] */
    $categoryList = $rootCategory->getChildren();

    $this->setVar('productCategory', $productCategory);
    $this->setVar('categoryTree', $categoryTree);
    $this->setVar('categoryList', $categoryList);
    $this->setVar('rootCategory', $rootCategory);
    $this->setVar('productFilter', $productFilter);
    $this->setVar('quantity', $rootCategory->getProductCount());
  }

  public function executeCategoryTag(sfWebRequest $request)
  {
    $requestCategory = $this->getProductCategory($request);
    $categoryTree = RepositoryManager::getProductCategory()->getTree(
      $requestCategory->core_id,
      $requestCategory->level + 2, // site-db level less per 1, and need load next level
      true
    );
    /** @var $currentCategory ProductCategoryEntity */
    /** @var $childrenCategory ProductCategoryEntity */
    $currentCategory = reset($categoryTree);
    $currentCategory = $currentCategory->getNode($requestCategory->core_id);

    $productFilter = $this->getProductFilter($request);
    $maxPerPage = 3;
    $viewList = RepositoryManager::getProductCategoryTagView()->getListByCategory(
      $currentCategory->getChildren(),
      $productFilter->getCoreProductFilter(false),
      array(),
      0,
      $maxPerPage * 2
    );

    // set request filter as link to child directory
    $f = $productFilter->getName();
    if($request->hasParameter($f)){
      $requestData = array( $f => $request->getParameter($f) );
      foreach($viewList as $view)
        $view->setRequest($requestData);
    }

    $this->setVar('maxPerPage', $maxPerPage);
    $this->setVar('categoryTree', $categoryTree);
    $this->setVar('categoryTagList', $viewList);
    $this->setVar('productFilter', $productFilter);
    $this->setVar('quantity', $currentCategory->getProductCount());
  }

  public function executeCarousel(sfWebRequest $request)
  {
    $this->setLayout(false);
    $productCategory = $this->getProductCategory($request, false);
    $request->setParameter('num', 3);

    $productPager = $this->getProductPager($request);
    CoreClient::getInstance()->execute();

    foreach ($productPager->getResults() as $item)
    {
      $this->renderPartial('product_/show_', array('view' => $productCategory->has_line ? 'line' : 'compact', 'item' => $item));
    }

    return sfView::NONE;
  }

  public function executeCount(sfWebRequest $request)
  {
    $productFilter = $this->getProductFilter($request);
    $data = RepositoryManager::getListing()->getListing(
      $productFilter->getCoreProductFilter(),
      array(),
      null,
      null,
      true
    );

    return $this->renderJson(array(
      'success' => true,
      'data' => $data['count'],
    ));
  }

  public function executeLine(sfWebRequest $request)
  {
    $this->loadList($request);

    $productCategory = $this->getProductCategory($request);
    // generate title
    $title = $productCategory['name'];
    if ($request->getParameter('page')) {
      $title .= ' – ' . $request->getParameter('page');
    }
    $rootCategory = $productCategory->getRootCategory();
    if ($rootCategory->id !== $productCategory->id) {
      $title .= ' – ' . $rootCategory;
    }
    /** @var $response sfWebResponse */
    $response = $this->getResponse();
    $response->setTitle($title . ' – Enter.ru');
  }

  public function executeCategoryAjax(sfWebRequest $request)
  {
    $this->setVar('allOk', false);
    $this->setVar('ajax_flag', true);
    $this->getProductPager($request);
    CoreClient::getInstance()->execute();
    $this->setVar('allOk', true);
  }

  /**
   * @param sfWebRequest $request
   * @return ProductCorePagerContainer
   */
  private function getProductPager(sfWebRequest $request)
  {
    $productFilter = $this->getProductFilter($request);

    // sorting
    $productSorting = new ProductSorting();
    $active = array_pad(explode('-', $request->getParameter('sort')), 2, null);
    $productSorting->setActive($active[0], $active[1]);

    // load listing data
    $maxPerPage = $request->getParameter('num', sfConfig::get('app_product_max_items_on_category', 20));
    $page = $request->getParameter('page', 1);
    $productPager = new ProductCorePagerContainer($maxPerPage);
    $productPager->setPage($page);

    RepositoryManager::getListing()->getListingAsync(function($data) use(&$productPager){
        $count = $data['count'];
        sfContext::getInstance()->getLogger()->info(print_r($data,1));
        RepositoryManager::getProduct()->getListByIdAsync(function($models) use(&$productPager, $count){
          /** @var $productPager ProductCorePagerContainer */
          $productPager->setResult($models, $count);
          $productPager->init();
        }, $data['list'], true);
      },
      $productFilter->getCoreProductFilter(),
      $productSorting->getCoreSort(),
      ($page-1) * $maxPerPage,
      $maxPerPage
    );

    $productCategory = $this->getProductCategory($request, false);
    if ($productCategory->has_line) {
      $this->setVar('view', 'line');
      $this->setVar('list_view', false);
    }
    else {
      $this->setVar('view', $request->getParameter('view', $productCategory->product_view));
    }
    $this->setVar('noInfinity', true);
    $this->setVar("productFilter", $productFilter);
    $this->setVar("productSorting", $productSorting);
    $this->setVar('productPager', $productPager);
    return $productPager;
  }

  /**
   * @param sfWebRequest $request
   */
  private function loadList(sfWebRequest $request)
  {
    $productCategory = $this->getProductCategory($request, false);
    $productPager = $this->getProductPager($request);

    // load category
    $this->loadCategoryTree($productCategory);
    CoreClient::getInstance()->execute();

    $this->forward404If($productPager->getPage() > 1 && $productPager->getPage() > $productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  private function loadCategoryTree(ProductCategory $productCategory)
  {
    $self = $this;
    RepositoryManager::getProductCategory()->getTreeAsync(function($categoryTree) use(&$self, &$productCategory){
        /** @var $rootCategory ProductCategoryEntity */
        /** @var $productCategory ProductCategory */
        /** @var $self myActions */
        $rootCategory = reset($categoryTree);
        if($node = $rootCategory->getNode($productCategory->core_id)){
          $quantity = $node->getProductCount();
        }
        else{
          $quantity = 0;
        }
        $self->setVar('quantity', $quantity);
        $self->setVar('categoryTree', $categoryTree);
      },
      $productCategory->hasChildren() ? $productCategory->core_id : $productCategory->core_parent_id,
      $productCategory->level + 1,
      true
    );
  }

  private function getProductFilter(sfWebRequest $request)
  {
    $productFilter = new ProductCoreFormFilterSimple($this->getProductCategory($request, false));
    $productFilter->setValues($request->getParameter($productFilter->getName(), array()));
    return $productFilter;
  }

  /**
   * @param sfWebRequest $request
   * @throws sfException
   */
  private function seoRedirectOnPageDublicate(sfWebRequest $request)
  {
    /** @var $route sfObjectRoute */
    $route = $this->getRoute();
    /** @var $productCategory ProductCategory */
    $productCategory = $route->getObject();
    $view = $productCategory->product_view;
    if (empty($view)) $view = 'compact';
    //если передано page=1 или view c дефолным значением, отрезаем этот параметр и делаем редирект.
    //необходимо для seo
    $redirectAr = array(
      'page' => 1,
      'view' => $view,
    );
    foreach ($redirectAr as $key => $val)
    {
      $currentVal = $request->getParameter($key);
      //если требуется редирект с этой страницы
      if (isset($currentVal) && $currentVal == $val) {
        $uri = $request->getUri();
        if (strpos($uri, '&') === false) {
          $replaceStr = "?$key=$val";
        }
        else
        {
          $replaceStr = array("$key=$val&", "&$key=$val");
        }
        $uri = str_replace($replaceStr, '', $request->getUri());
        $this->redirect($uri);
      }
    }
  }

  /**
   * @param sfWebRequest $request
   * @param bool $checkRedirect
   * @return ProductCategory
   * @throws sfException
   */
  private function oldUrlRedirect(sfWebRequest $request, $checkRedirect = true)
  {
    try
    {
      /** @var $route sfObjectRoute */
      $route = $this->getRoute();
      /** @var $productCategory ProductCategory */
      $productCategory = $route->getObject();

      // 301-й редирект. Можно удалить 01.02.2012
      if ($checkRedirect) {
        if (false === strpos($request['productCategory'], '/')) {
          if (!empty($productCategory->token_prefix)) {
            $this->redirect('productCatalog_category', $productCategory, 301);
          }
        }
      }
      return $productCategory;
    }
    catch (sfError404Exception $e)
    {
      return $this->forward('redirect', 'index');
    }
  }

  private $productCategoryCache;

  /**
   * @param $request
   * @param bool $checkRedirect
   * @return ProductCategory
   */
  private function getProductCategory($request, $checkRedirect = true)
  {
    if (!$this->productCategoryCache) {
      $this->productCategoryCache = $this->oldUrlRedirect($request, $checkRedirect);
      $this->setVar('productCategory', $this->productCategoryCache);
    }
    return $this->productCategoryCache;
  }
}

class ProductCorePagerContainer extends sfPager
{
  public function __construct($maxPerPage = 10)
  {
    parent::__construct('Product', $maxPerPage);
  }

  public function setResult($results, $count){
    $this->results = (array)$results;
    $this->setNbResults((int)$count);
  }

  public function init()
  {
    if($this->getMaxPerPage()){
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  public function getResults()
  {
    return $this->results;
  }

  protected function retrieveObject($offset)
  {
    return $this->results[$offset];
  }
}

class ProductCoreFormFilterSimple
{
  const NAME = 'f';
  /** @var \ProductCategory */
  private $productCategory;
  /** @var \ProductCategoryFilterEntity[] */
  private $filterList;
  private $values = array();
  private $name = self::NAME;

  /**
   * @param ProductCategory $category
   */
  public function __construct(ProductCategory $category)
  {
    $this->productCategory = $category;

    $this->filterList = RepositoryManager::getProductCategoryFilter()->getList(
      $this->productCategory->core_id
    );
  }

  private function getUrl($filterId, $value = null)
  {
    $data = $this->values;
    if (array_key_exists($filterId, $data)) {
      if (null == $value) {
        unset($data[$filterId]);
      }
      else foreach ($data[$filterId] as $k => $v)
      {
        if ($v == $value) {
          unset($data[$filterId][$k]);
        }
      }
    }
    $token = $this->productCategory->token;
    if ($this->productCategory->token_prefix) {
      $token = $this->productCategory->token_prefix . '/' . $token;
    }
    return url_for('productCatalog_category', array(
      'productCategory' => $token,
      $this->name => $data
    ));
  }

  /**
   * Mapper from current front-end listing filter to core listing filter API
   * @param bool $useCategoryFilter
   * @return array
   */
  public function getCoreProductFilter($useCategoryFilter = true)
  {
    $filters = array();

    foreach ($this->filterList as $filter) {
      $value = $this->getValue($filter);
      if (!empty($value)) {
        switch ($filter->getTypeId()) {
          case ProductCategoryFilterEntity::TYPE_NUMBER:
          case ProductCategoryFilterEntity::TYPE_SLIDER:
            if ($filter->getMax() != $value['to'] || $filter->getMin() != $value['from']) {
              $filters[] = array($filter->getFilterId(), 2, $value['from'], $value['to']);
            }
            break;
          default:
            $filters[] = array($filter->getFilterId(), 1, $value);
            break;
        }
      }
    }

    if (empty($filters)) {
      $filters[] = array('is_model', 1, array(true));
    }

    $filters[] = array('is_view_list', 1, array(true));

    if ($this->productCategory && $useCategoryFilter) {
      $filters[] = array('category', 1, $this->productCategory->core_id);
    }

    return $filters;
  }

  public function getSelectedList()
  {
    $list = array();
    foreach ($this->filterList as $filter) {
      $value = $this->getValue($filter);
      switch ($filter->getTypeId()) {
        case ProductCategoryFilterEntity::TYPE_SLIDER:
        case ProductCategoryFilterEntity::TYPE_NUMBER:
          if (empty($value['from']) && empty($value['to'])) continue;
          $name = array();
          if (!($this->isEqualNumeric($value['from'], $filter->getMin()))) $name[] = sprintf('от %d', $value['from']);
          if (!($this->isEqualNumeric($value['to'], $filter->getMax()))) $name[] = sprintf('до %d', $value['to']);
          if (!$name) continue;
          if ($filter->getFilterId() == 'price') $name[] .= 'р.';
          $list[] = array(
            'type' => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
            'name' => join(' ', $name),
            'url' => $this->getUrl($filter->getFilterId()),
            'title' => $filter->getName(),
          );
          break;
        case ProductCategoryFilterEntity::TYPE_BOOLEAN:
          if (!is_array($value) || count($value) == 0) continue;
          $list[] = array(
            'type' => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
            'name' => $filter->getName() . ': ' . reset($value) == 1 ? 'да' : 'нет',
            'url' => $this->getUrl($filter->getFilterId()),
            'title' => $filter->getName(),
          );
          break;
        case ProductCategoryFilterEntity::TYPE_LIST:
          if (!is_array($value) || count($value) == 0) continue;
          foreach ($filter->getOptionList() as $option)
            if (in_array($option['id'], $value))
              $list[] = array(
                'type' => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
                'name' => $option['name'],
                'url' => $this->getUrl($filter->getFilterId(), $option['id']),
                'title' => $filter->getName(),
              );
          break;
        default:
          continue;
      }
    }
    return $list;
  }

  /**
   * @param array $values
   */
  public function setValues(array $values)
  {
    $this->values = $values;
  }

  /**
   * @param ProductCategoryFilterEntity $filter
   * @return mixed|null
   */
  public function getValue(ProductCategoryFilterEntity $filter)
  {
    if (isset($this->values[$filter->getFilterId()]))
      return (array)$this->values[$filter->getFilterId()];
    else
      return array();
  }

  public function getValueMin(ProductCategoryFilterEntity $filter)
  {
    $value = $this->getValue($filter);
    if (isset($value['from'])) {
      return $value['from'];
    }
    else {
      return $filter->getMin();
    }
  }

  public function getValueMax(ProductCategoryFilterEntity $filter)
  {
    $value = $this->getValue($filter);
    if (isset($value['to'])) {
      return $value['to'];
    }
    else {
      return $filter->getMax();
    }
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return ProductCategoryFilterEntity[]
   */
  public function getFilterList()
  {
    return $this->filterList;
  }

  /**
   * @return ProductCategory
   */
  public function getProductCategory()
  {
    return $this->productCategory;
  }

  private function isEqualNumeric($first, $second)
  {
    $first = myToolkit::clearZero((float)$first);
    $second = myToolkit::clearZero((float)$second);
    return $first == $second;
  }
}