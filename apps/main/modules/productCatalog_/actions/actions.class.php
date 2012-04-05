<?php

/**
 * productCatalog_ actions.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 *
 * @property ProductCorePager $productPager
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

  public function executeIndex()
  {
    $productCategoryList = ProductCategoryTable::getInstance()->createQuery()
      ->select('id, name, level, token, token_prefix')
      ->where('is_active = ?', true)
      ->orderBy('root_id, lft')
      ->fetchArray();

    $this->setVar('productCategoryList', $productCategoryList);
    $this->setVar('infinity', true);
  }

  public function executeProductType(sfWebRequest $request)
  {
    $productType = !empty($request['productType']) ? ProductTypeTable::getInstance()->getById($request['productType']) : false;
    $this->forward404Unless($productType);
    $this->loadList($request, array(
      'productType' => $productType
    ));
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

    // если категория корневая
    if ($productCategory->getNode()->isRoot()) {
      $this->setTemplate('categoryRoot');
    }

    $this->forward($this->getModuleName(), 'categoryTag');
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
    $viewList = RepositoryManager::getProductCategoryTagView()->getListByCategory(
      $currentCategory->getChildren(),
      $productFilter->getCoreProductFilter(),
      array(),
      0,
      6
    );

    $this->setVar('categoryTree', $categoryTree);
    $this->setVar('categoryTagList', $viewList);
    $this->setVar('productFilter', $productFilter);
  }

  public function executeCount(sfWebRequest $request)
  {
    $productCategory = $this->getProductCategory($request);
    if ($productCategory->hasChildren())
      $this->forward('productCatalog', 'count');

    $productFilter = $this->getProductFilter($request);
    $productPager = new ProductCorePager(0);
    $productPager->setProductFilter($productFilter);
    $productPager->init(false);

    return $this->renderJson(array(
      'success' => true,
      'data' => $productPager->count(),
    ));
  }

  public function executeLine(sfWebRequest $request)
  {
    $this->loadList($request);
    $this->setVar('list_view', false);
    $this->setVar('view', 'line');

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

  // @todo remove after implement all to core api
  public function executeTag(sfWebRequest $request)
  {
    $this->forward('productCatalog', 'tag');
  }

  public function executeCategoryAjax(sfWebRequest $request)
  {
    $this->setVar('allOk', false);
    $this->loadList($request);
    $this->setVar('allOk', true);
  }

  // @todo implement in core api
  public function executeCreator(sfWebRequest $request)
  {
    $this->forward('productCatalog', 'creator');
  }

  /**
   * @param sfWebRequest $request
   */
  private function loadList(sfWebRequest $request)
  {
    $loadListTimer = sfTimerManager::getTimer('$loadListTimer');
    $productFilterTimer = sfTimerManager::getTimer('$productFilterTimer');
    $productFilter = $this->getProductFilter($request);
    $productFilterTimer->addTime();


    // sorting
    $productSortingTimer = sfTimerManager::getTimer('$productSortingTimer');
    $productSorting = new myProductSorting();
    $active = array_pad(explode('-', $this->getRequest()->getParameter('sort')), 2, null);
    $productSorting->setActive($active[0], $active[1]);
    $productSortingTimer->addTime();

    // pager
    $productPagerTimer = sfTimerManager::getTimer('$productPager');
    $productPager = new ProductCorePager(
      $request->getParameter('num', sfConfig::get('app_product_max_items_on_category', 20))
    );

    $productPager->setProductFilter($productFilter);
    $productPager->setProductSort($productSorting);
    $productPager->setPage($page = $this->getRequest()->getParameter('page', 1));
    $productPager->init();
    $productPagerTimer->addTime();
    $loadListTimer->addTime();

    $category = $this->getProductCategory($request);
    $categoryTree = RepositoryManager::getProductCategory()->getTree(
      $category->hasChildren() ? $category->core_id : $category->core_parent_id,
      $category->level + 1,
      true
    );

    sfContext::getInstance()->getLogger()->info('$productFilterTimer at ' . $productFilterTimer->getElapsedTime());
    sfContext::getInstance()->getLogger()->info('$productSortingTimer at ' . $productSortingTimer->getElapsedTime());
    sfContext::getInstance()->getLogger()->info('$productPagerTimer at ' . $productPagerTimer->getElapsedTime());
    sfContext::getInstance()->getLogger()->info('$loadListTimer at ' . $loadListTimer->getElapsedTime());

    $this->setVar('view', $request->getParameter('view', $this->getProductCategory($request)->product_view));
    $this->setVar("productFilter", $productFilter);
    $this->setVar("productSorting", $productSorting);
    $this->setVar('noInfinity', true);
    $this->setVar('productPager', $productPager);
    $this->setVar('quantity', $productPager->getNbResults());
    $this->setVar('categoryTree', $categoryTree);

    $this->forward404If($page > 1 && $page > $productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  private function getProductFilter(sfWebRequest $request)
  {
    $productFilter = new ProductCoreFormFilterSimple($this->getProductCategory($request));
    $productFilter->setValues($request->getParameter($productFilter->getName(), array()));
    return $productFilter;
  }

  /**
   * @param sfWebRequest $request
   * @throws sfException
   */
  private function seoRedirectOnPageDublicate(sfWebRequest $request)
  {
    //если передано page=1 или view=compact, отрезаем этот параметр и делаем редирект.
    //необходимо для seo
    $redirectAr = array(
      'page' => 1,
      'view' => 'compact'
    );
    foreach ($redirectAr as $key => $val)
    {
      $currentVal = $request->getParameter($key);
      //если требуется редирект с этой страницы
      if (isset($currentVal) && $currentVal == $val) {
        $uri = $this->getRequest()->getUri();
        if (strpos($uri, '&') === false) {
          $replaceStr = "?$key=$val";
        }
        else
        {
          $replaceStr = array("$key=$val&", "&$key=$val");
        }
        $uri = str_replace($replaceStr, '', $this->getRequest()->getUri());
        $this->redirect($uri);
      }
    }
  }

  /**
   * @param sfWebRequest $request
   * @return ProductCategory
   * @throws sfException
   */
  private function oldUrlRedirect(sfWebRequest $request)
  {
    try
    {
      /** @var $route sfObjectRoute */
      $route = $this->getRoute();
      /** @var $productCategory ProductCategory */
      $productCategory = $route->getObject();

      // 301-й редирект. Можно удалить 01.02.2012
      if (false === strpos($request['productCategory'], '/')) {
        if (!empty($productCategory->token_prefix)) {
          $this->redirect('productCatalog__category', $productCategory, 301);
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
   * @return ProductCategory
   */
  private function getProductCategory($request)
  {
    if (!$this->productCategoryCache) {
      $this->productCategoryCache = $this->oldUrlRedirect($request);
      $this->setVar('productCategory', $this->productCategoryCache);
    }
    return $this->productCategoryCache;
  }
}

/**
 * Custom pager for use Core listing API
 */
class ProductCorePager extends sfPager
{
  /** @var ProductCoreFormFilterSimple */
  private $filter;
  /** @var myProductSorting */
  private $sort;
  private $result;

  public function __construct($maxPerPage = 10)
  {
    parent::__construct('Product', $maxPerPage);
  }

  public function setProductFilter(ProductCoreFormFilterSimple $filter)
  {
    $this->filter = $filter;
  }

  public function setProductSort(myProductSorting $sort)
  {
    $this->sort = $sort;
  }

  /**
   * Initialize the pager.
   *
   * Function to be called after parameters have been set.
   * @param bool $loadData
   */
  public function init($loadData = true)
  {
    $response = RepositoryManager::getListing()->getListing(
      $this->filter->getCoreProductFilter(),
      $this->getCoreSort(),
      ($this->getPage() - 1) * $this->getMaxPerPage(),
      $this->getMaxPerPage()
    );
    $this->setNbResults($response['count']);
    $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    if ($loadData) {
      $this->result = RepositoryManager::getProduct()->getListById($response['list'], true);
    }
  }

  /**
   * Returns an array of results on the given page.
   *
   * @return ProductEntity[]
   */
  public function getResults()
  {
    return $this->result;
  }

  protected function retrieveObject($offset)
  {
    // TODO: Implement retrieveObject() method.
    return null;
  }

  /**
   * Mapper from current front-end sorting to core listing sort API
   * @return array
   */
  protected function getCoreSort()
  {
    if ($this->sort) {
      $active = $this->sort->getActive();
      return array($active['name'] => $active['direction']);
    }
    else {
      return array();
    }
  }
}

class ProductCoreFormFilterSimple
{
  /** @var \ProductCategory */
  private $productCategory;
  /** @var \ProductCategoryFilterEntity[] */
  private $filterList;
  private $values = array();
  private $name = 'f';

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
    return url_for('productCatalog__category', array(
      'productCategory' => $token,
      $this->name => $data
    ));
  }

  /**
   * Mapper from current front-end listing filter to core listing filter API
   * @return array
   */
  public function getCoreProductFilter()
  {
    $filters = array(
      array('is_view_list', 1, array(true)),
      array('is_model', 1, array(true)),
    );

    if ($this->productCategory) {
      $filters[] = array('category', 1, $this->productCategory->core_id);
    }

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