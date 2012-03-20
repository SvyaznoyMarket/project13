<?php

/**
 * productCatalog_ actions.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 *
 * @property ProductCorePager $productPager
 */
class productCatalog_Actions extends myActions
{
  public function preExecute()
  {
    parent::postExecute();
    $this->getRequest()->setParameter('_template', 'product_catalog');
  }

  /**
   * Executes index action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $productCategoryList = ProductCategoryTable::getInstance()->createQuery()
      ->select('id, name, level, token, token_prefix')
      ->where('is_active = ?', true)
      ->orderBy('root_id, lft')
      ->fetchArray();

    $this->setVar('productCategoryList', $productCategoryList);
    $this->setVar('infinity', true);
  }

  public function executeFilter(sfWebRequest $request)
  {
    $this->loadList($request);
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
    $timer = sfTimerManager::getTimer(__METHOD__);
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
    $timer->addTime();
  }

  public function executeCategory(sfWebRequest $request)
  {
    if (!$request->isXmlHttpRequest())
      $this->seoRedirectOnPageDublicate($request);

    if (!$request->isXmlHttpRequest())
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
  }

  public function executeCount(sfWebRequest $request)
  {
    $productFilter = $this->getProductFilter($request, array());

    $productPager = new ProductCorePager(0, array(), $this->getUser());

    $productPager->setProductFilter($productFilter);
    $productPager->init();

    return $this->renderJson(array(
      'success' => true,
      'data' => $productPager->count(),
    ));
  }

  /**
   * @param sfWebRequest $request
   * @param array $options
   */
  private function loadList(sfWebRequest $request, array $options = array())
  {
    sfContext::getInstance()->getLogger()->info("Begin " . __METHOD__);
    $fullTimer = sfTimerManager::getTimer(__METHOD__);
    $filterTimer = sfTimerManager::getTimer(__METHOD__ . " filter");
    sfContext::getInstance()->getLogger()->info("Filter " . __METHOD__);
    $productFilter = $this->getProductFilter($request, $options);
    $filterTimer->addTime();
    // sorting
    $sortingTimer = sfTimerManager::getTimer(__METHOD__ . " sorting");
    sfContext::getInstance()->getLogger()->info("Sorting " . __METHOD__);
    $productSorting = new myProductSorting();
    $active = array_pad(explode('-', $this->getRequest()->getParameter('sort')), 2, null);
    $productSorting->setActive($active[0], $active[1]);
    $sortingTimer->addTime();
    // pager
    $pagerTimer = sfTimerManager::getTimer(__METHOD__ . " pager");
    sfContext::getInstance()->getLogger()->info("Pager create " . __METHOD__);
    $productPager = new ProductCorePager(
      $request->getParameter('num', sfConfig::get('app_product_max_items_on_category', 20)),
      array(
        'with_properties' => 'expanded' == $request['view'] ? true : false,
        'property_view' => 'expanded' == $request['view'] ? 'list' : false,
        // 'view' => 'list',
        // 'with_line' => 'line' == $request['view'] ? true : false,
        // 'with_model' => true,
        'hydrate_array' => true,
      ),
      $this->getUser()
    );

    sfContext::getInstance()->getLogger()->info("Pager init " . __METHOD__);
    $productPager->setProductFilter($productFilter);
    $productPager->setProductSort($productSorting);
    $productPager->setPage($page = $this->getRequest()->getParameter('page', 1));
    $productPager->init();
    $pagerTimer->addTime();

    $this->setVar('view', $request->getParameter('view', $this->getProductCategory($request)->product_view));
    $this->setVar("productFilter", $productFilter);
    $this->setVar("productSorting", $productSorting);
    $this->setVar('noInfinity', true);
    $this->setVar('productPager', $productPager);

    $fullTimer->addTime();
    sfContext::getInstance()->getLogger()->info("End " . __METHOD__);
    sfContext::getInstance()->getLogger()->info("Full time " . $fullTimer->getElapsedTime() . " " . __METHOD__);
    sfContext::getInstance()->getLogger()->info("filter time " . $filterTimer->getElapsedTime() . " " . __METHOD__);
    sfContext::getInstance()->getLogger()->info("sorting time " . $sortingTimer->getElapsedTime() . " " . __METHOD__);
    sfContext::getInstance()->getLogger()->info("pager time " . $pagerTimer->getElapsedTime() . " " . __METHOD__);
    $this->forward404If($page > 1 && $page > $productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }

  private function getProductFilter(sfWebRequest $request, array $options = array())
  {
    /** @var $category ProductCategory */
    $category = $this->getProductCategory($request);
    $withCreator = !in_array($category->getRootCategory()->token, array('jewel', 'furniture',));

    $productFilter = new ProductCoreFormFilter(
      array(),
      myToolkit::arrayDeepMerge(array(
        'productCategory' => $category,
        'with_creator' => $withCreator,
        'region_id' => $this->getUser()->getRegion('core_id'),
      ), $options)
    );
    $productFilter->bind($request->getParameter($productFilter->getName()));
    $this->setVar("productCategory", $category);
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
  /** @var ProductCoreFormFilter */
  private $filter;
  /** @var myProductSorting */
  private $sort;
  private $result;
  private $user;
  private $queryParams = array();

  public function __construct($maxPerPage = 10, array $queryParams = array(), myUser $user)
  {
    parent::__construct('Product', $maxPerPage);
    $this->queryParams = array_merge(
      $queryParams,
      array(
        'order' => '_index',
      )
    );
    $this->user = $user;
  }

  public function setProductFilter(ProductCoreFormFilter $filter)
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
   */
  public function init()
  {
    $query = array(
      "filter" => array(
        'filters' => $this->getCoreProductFilter(),
        'sort' => $this->getCoreSort(),
        'offset' => ($this->getPage() - 1) * $this->getMaxPerPage(),
        'limit' => $this->getMaxPerPage(),
      ),
      "region_id" => $this->user->getRegion('core_id'),
    );

    $response = CoreClient::getInstance()->query("listing.list", $query);
    $this->setNbResults($response['count']);
    $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

    /** @var $table ProductTable */
    $this->result = ProductTable::getInstance()->getListByCoreIds($response['list'], $this->queryParams);
  }

  /**
   * Returns an array of results on the given page.
   *
   * @return Product[]
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
   * Mapper from current front-end listing filter to core listing filter API
   * @return array
   */
  protected function getCoreProductFilter()
  {
    if (!$this->filter)
      return array();

    $filters = array(
      array('is_view_list', 1, array(true)),
      array('is_model', 1, array(true)),
    );

    if ($productCategory = $this->filter->getOption('productCategory')) {
      $filters[] = array('category', 1, $productCategory->core_id);
    }

    $filterList = $this->filter->getFilterList();
    $filterValues = $this->filter->getValues();

    foreach ($filterList as $filter) {
      $id = $filter->getFilterId();
      if (!empty($filterValues[$id])) {
        $value = $filterValues[$id];
        switch ($filter->getTypeId()) {
          case ProductCoreFilterItem::TYPE_NUMBER:
          case ProductCoreFilterItem::TYPE_SLIDER:
            if ($filter->getMax() != $value['to'] || $filter->getMin() != $value['from']) {
              $filters[] = array($id, 2, $value['from'], $value['to']);
            }
            break;
          default:
            $filters[] = array($id, 1, $value);
            break;
        }
      }
    }
    return $filters;
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

class ProductCoreFormFilter extends sfFormFilter
{
  /** @var ProductCoreFilterItem[] */
  private $filterList;

  public function configure()
  {
    parent::configure();
    $this->setOption('mark_required', false);
    $this->disableCSRFProtection();

    /** @var $productCategory ProductCategory */
    if (!$productCategory = $this->getOption('productCategory')) {
      throw new InvalidArgumentException('You must provide a productCategory object.');
    }

    $this->filterList = $this->loadCoreFilter($this->getOption('region_id'), $productCategory->core_id);
    foreach ($this->filterList as $filter) {
      $widget = null;
      switch ($filter->getTypeId()) {
        case ProductCoreFilterItem::TYPE_BOOLEAN:
          $widget = $this->getWidgetCheckbox();
          break;
        case ProductCoreFilterItem::TYPE_LIST:
          $widget = $this->getWidgetChoice($filter);
          break;
        case ProductCoreFilterItem::TYPE_NUMBER:
        case ProductCoreFilterItem::TYPE_SLIDER:
          $widget = $this->getWidgetRange($filter);
          $widget->setDefault(array(
            'from' => myToolkit::trimZero($filter->getMin()),
            'to' => myToolkit::trimZero($filter->getMax()),
          ));
          break;
        default:
          continue;
      }
      if ($widget) {
        $widget->setOption('label', $filter->getName());
        if ($filter->getFilterId() == 'brand') {
          $widget->addOption('renderer_class', 'myWidgetFormSelectCheckbox');
          $widget->addOption('renderer_options', array(
            'formatter' => array($this, 'show_part'),
            'label_separator' => '',
          ));
        }
        $index = $filter->getFilterId();
        $this->setWidget($index, $widget);
        $this->setValidator($index, new sfValidatorPass());
      }
    }

    $productType = $this->getOption('productType', null);
    if ($productType) {
      $this->widgetSchema['type'] = new sfWidgetFormInputHidden();
      $this->validatorSchema['type'] = new sfValidatorPass();
      $this->setDefault('type', $productType->id);
    }

    $this->widgetSchema->setNameFormat('f[%s]');
  }

  /**
   * @return ProductCoreFilterItem[]
   */
  public function getFilterList()
  {
    return $this->filterList;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if (!is_array($taintedValues))
      $taintedValues = array();
    // fix for range widgets than has been unseted
    foreach ($this as $index => $field)
    {
      if (($field->getWidget() instanceof myWidgetFormRange) && !isset($taintedValues[$index])) {
        $taintedValues[$index] = $field->getWidget()->getDefault();
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  /**
   * @param $productFilter
   * @return myWidgetFormChoice
   */
  protected function getWidgetChoice(ProductCoreFilterItem $productFilter)
  {
    $choices = array();
    foreach ($productFilter->getOptions() as $productPropertyOption)
    {
      $choices[$productPropertyOption['id']] = $productPropertyOption['name'];
    }
    return new myWidgetFormChoice(array(
      'choices' => $choices,
      'multiple' => $productFilter->getIsMultiple(),
      'expanded' => true,
      'renderer_class' => 'myWidgetFormSelectCheckbox',
      'renderer_options' => array(
        'label_separator' => '',
        'formatter' => array($this, 'show_part'),
      ),
    ));
  }

  /**
   * @param $productFilter
   * @return myWidgetFormRange
   */
  protected function getWidgetRange(ProductCoreFilterItem $productFilter)
  {
    $id = uniqid();
    $template = ''
      . '<div class="bSlide">'
      . '%value_from% %value_to%'
      . '<div class="sliderbox">'
      . '<div id="slider-' . $id . '" class="filter-range"></div>'
      . '</div>'
      . '<div class="pb5">'
      . '<input class="slider-from" type="hidden" disabled="disabled" value="' . myToolkit::trimZero($productFilter->getMin()) . '" />'
      . '<input class="slider-to" type="hidden" disabled="disabled" value="' . myToolkit::trimZero($productFilter->getMax()) . '" />'
      . '<span class="slider-interval"></span> ' . ($productFilter->getUnit() ? $productFilter->getUnit() : '<span class="rubl">p</span>')
      . '</div>'
      . '</div>'
      . '<div class="clear"></div>';

    return new myWidgetFormRange(array(
      'value_from' => myToolkit::trimZero($productFilter->getMin()),
      'value_to' => myToolkit::trimZero($productFilter->getMax()),
      'template' => $template
    ), array(
      'class' => 'text',
      'style' => 'display: inline; width: 60px;',
    ));
  }

  /**
   * @return myWidgetFormChoice
   */
  protected function getWidgetCheckbox()
  {
    //return new myWidgetFormInputCheckbox();
    return new myWidgetFormChoice(array(
      'choices' => array(1 => 'да', 0 => 'нет'),
      'multiple' => true,
      'expanded' => true,
      //'renderer_class'   => 'myWidgetFormSelectCheckbox',
      'renderer_options' => array(
        'label_separator' => '',
      ),
    ));
  }

  public function show_part(sfWidget $widget, $inputs)
  {
    $rows = array();
    $shown = array_slice($inputs, 0, 5);
    foreach ($shown as $input)
    {
      $rows[] = $widget->renderContentTag('li', $input['input'] . $widget->getOption('label_separator') . $input['label']);
    }
    if (count($inputs) > 5) {
      $hidden = array_slice($inputs, 5);
      foreach ($hidden as $input)
      {
        $rows[] = $widget->renderContentTag('li', $input['input'] . $widget->getOption('label_separator') . $input['label'], array('class' => 'hf', 'style' => 'display: none',));
      }
      $rows[] = $widget->renderContentTag('li', '<a href="#">еще...</a>', array('class' => 'bCtg__eMore', 'style' => 'padding-left: 10px;'));
    }

    return !$rows ? '' : $widget->renderContentTag('ul', implode($widget->getOption('separator'), $rows), array('class' => $widget->getOption('class')));
  }

  /**
   * @param $regionId
   * @param $categoryId
   * @return ProductCoreFilterItem[]
   */
  private function loadCoreFilter($regionId, $categoryId)
  {
    $list = array();

    foreach (
      CoreClient::getInstance()->query('listing.filter', array(
        'region_id' => (int)$regionId,
        'category_id' => (int)$categoryId,
      )) as $itemData) {
      $list[] = new ProductCoreFilterItem($itemData);
    }
    return $list;
  }

  public function getSingleCreator()
  {
    $creator = null;

    if (!empty($this->values['creator']) && (1 == count($this->values['creator']))) {
      $creator = CreatorTable::getInstance()->getById($this->values['creator'][0]);
    }

    return $creator;
  }
}

/**
 * Product filter item
 */
class ProductCoreFilterItem
{
  const TYPE_BOOLEAN = 1;
  const TYPE_DATE = 2;
  const TYPE_NUMBER = 3;
  const TYPE_STRING = 4;
  const TYPE_LIST = 5;
  const TYPE_SLIDER = 6;

  private $filter_id;
  private $type_id;
  private $name;
  private $unit;
  private $is_multiple;
  private $is_slider;
  private $min;
  private $max;
  private $options = array();

  public function __construct(array $data = array())
  {
    if (array_key_exists('filter_id', $data)) $this->setFilterId($data['filter_id']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
    if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
    if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
    if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
    if (array_key_exists('is_slider', $data)) $this->setIsSlider($data['is_slider']);
    if (array_key_exists('min', $data)) $this->setMin($data['min']);
    if (array_key_exists('max', $data)) $this->setMax($data['max']);
    if (array_key_exists('options', $data)) $this->setOptions($data['options']);
  }

  public function toArray()
  {
    return array(
      'filter_id' => $this->filter_id,
      'name' => $this->name,
      'type_id' => $this->type_id,
      'unit' => $this->unit,
      'is_multiple' => $this->is_multiple,
      'is_slider' => $this->is_slider,
      'min' => $this->min,
      'max' => $this->max,
      'options' => $this->options,
    );
  }

  public function setFilterId($filter_id)
  {
    $this->filter_id = $filter_id;
  }

  public function getFilterId()
  {
    return $this->filter_id;
  }

  public function setIsMultiple($is_multiple)
  {
    $this->is_multiple = (boolean)$is_multiple;
  }

  public function getIsMultiple()
  {
    return $this->is_multiple;
  }

  public function setIsSlider($is_slider)
  {
    $this->is_slider = (boolean)$is_slider;
  }

  public function getIsSlider()
  {
    return $this->is_slider;
  }

  public function setMax($max)
  {
    $this->max = $max;
  }

  public function getMax()
  {
    return $this->max;
  }

  public function setMin($min)
  {
    $this->min = $min;
  }

  public function getMin()
  {
    return $this->min;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setOptions(array $options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setTypeId($type_id)
  {
    $this->type_id = (int)$type_id;
  }

  public function getTypeId()
  {
    return $this->type_id;
  }

  public function setUnit($unit)
  {
    $this->unit = $unit;
  }

  public function getUnit()
  {
    return $this->unit;
  }
}