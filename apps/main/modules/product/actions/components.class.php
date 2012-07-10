<?php

/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productComponents extends myComponents
{

  /**
   * Executes show component
   *
   * @param Product $product Товар
   * @param view $view Вид
   * @deprecated
   */
  public function executeShow()
  {
    if (!$this->product) {
      return sfView::NONE;
    }

    if (!in_array($this->view, array('expanded', 'compact', 'description', 'line', 'orderOneClick', 'stock', 'extra_compact'))) {
      $this->view = 'compact';
    }

    $this->maxPerPage = $this->maxPerPage ? : 3;

    // cache key
    $cacheKey = in_array($this->view, array('compact', 'expanded')) && sfConfig::get('app_cache_enabled', false) ? $this->getCacheKey(array(
      'product' => is_scalar($this->product) ? $this->product : $this->product['id'],
      'region' => $this->getUser()->getRegion('id'),
      'view' => $this->view,
      'i' => $this->ii,
      'maxPerPage' => $this->maxPerPage,
    )) : false;

    // checks for cached vars
    if ($cacheKey && $this->setCachedVars($cacheKey)) {
      //myDebug::dump($this->getVarHolder()->getAll(), 1);
      return sfView::SUCCESS;
    }

    $table = ProductTable::getInstance();

    if (is_scalar($this->product)) {
      $params = array(
        'hydrate_array' => true,
        'with_model' => true,
      );

      if ('expanded' == $this->view) {
        $params = myToolkit::arrayDeepMerge($params, array(
          'group_property' => false,
          'view' => 'list',
          'property_view' => 'list',
          'with_properties' => true,
        ));
      }
      else {
        $params = myToolkit::arrayDeepMerge($params, array(
          'view' => 'list',
          'with_line' => 'line' == $this->view ? true : false,
        ));
      }

      $this->product = $table->getById($this->product, $params);
      $this->product['is_insale'] = $table->isInsale($this->product);
    }

    $item = array(
      'id' => $this->product['id'],
      'core_id' => $this->product['core_id'],
      'token' => $this->product['token'],
      'barcode' => $this->product['barcode'],
      'article' => $this->product['article'],
      'name' => $this->product['name'],
      'creator' => (is_array($this->product['Creator']) || ($this->product['Creator'] instanceof Creator)) ? $this->product['Creator']['name'] : '',
      'rating' => $this->product['rating'],
      'price' => $table->getFormattedPrice($this->product), //$this->product->formatted_price,
      'avg_price' => $table->getFormattedPrice($this->product, 'avg'), //$this->product->formatted_price,
      'has_link' => $this->product['view_show'],
      'photo' => $table->getMainPhotoUrl($this->product, 2),
      'is_insale' => $this->product['is_insale'],
      'is_instock' => $this->product['is_instock'],
      'url' => $this->generateUrl('productCard', array('product' => $this->product['token_prefix'] . '/' . $this->product['token']), array('absolute' => true)),
      'label' => $this->product['Label']->getId() ? $this->product['Label'] : null,
    );

    if (in_array($this->view, array('compact', 'extra_compact'))) {
      $rootProductCategory = ProductCategoryTable::getInstance()->getRootRecord($this->product['Category'][0], array(
        'hydrate_array' => true,
        'select' => 'productCategory.id, productCategory.name',
      ));
      $item['root_name'] = $rootProductCategory ? $rootProductCategory['name'] : '';

      if ('extra_compact' == $this->view) {
        $item['photo'] = $table->getMainPhotoUrl($this->product, 1);
      }
    }

    if ('orderOneClick' == $this->view) {
      $item['photo'] = $this->product->getMainPhotoUrl(1);
    }

    if (in_array($this->view, array('expanded', 'compact',))) {
      $item['preview'] = $this->product['preview'];

      $item['variation'] = array();
      if ($this->product['is_model']) {
        foreach ($table->getModelProperty($this->product) as $property)
        {
          $item['variation'][] = mb_strtolower($property['name']);
        }
      }
      $item['variation'] = implode(', ', $item['variation']);
    }
    if (in_array($this->view, array('description'))) {
      $item['description'] = $this->product['description'];
    }
    if ('line' == $this->view) {
      $item['url'] = $this->generateUrl('lineCard', array('line' => $this->product['Line']['token'],), array('absolute' => true));
      $item['Line']['name'] = $this->product['Line']['name'];
      $item['Line']['count'] = ProductLineTable::getInstance()->getProductCountById($this->product['Line']['id']);
    }
    if ('stock' == $this->view) {
      $item['description'] = $this->product['description'];
      $length = strlen($item['description']);
      if ($length > 120) {
        $length = mb_strpos($item['description'], ' ', 120);
      }
      $item['description'] = mb_substr($item['description'], 0, $length);
      $item['description'] = $item['description'] . ((mb_strlen($this->product['description']) > mb_strlen($item['description'])) ? '...' : '');
    }

    $this->setVar('item', $item, true);

    // что это? нигде не используется
    /*
    $selectedServices = $this->getUser()->getCart()->getServicesByProductId($this->product['id']);
    $this->setVar('selectedServices', $selectedServices, true);
     */

    $this->setVar('keys', $table->getCacheEraserKeys($this->product, 'show', array('region' => $this->getUser()->getRegion('core_id'),)));

    //myDebug::dump($item, 1);

    // caches vars
    if ($cacheKey) {
      $this->cacheVars($cacheKey);
      $this->getCache()->addTag("product-{$this->product['id']}", $cacheKey);
    }
  }

  /**
   * Executes preview component
   *
   * @param Product $product Товар
   */
  public function executePreview()
  {

  }

  /**
   * Executes pager component
   *
   * @param myDoctrinePager $pager Листалка товаров
   * @deprecated
   */
  public function executePager()
  {
    $this->view = !empty($this->view) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', 'line'))) {
      $this->view = 'compact';
    }

    $list = $this->pager->getResults();

    $this->setVar('list', $list, true);
  }

  /**
   * Executes sorting component
   *
   * @param array $productSorting Сортировка списка товаров
   * @deprecated
   */
  public function executeSorting()
  {
    $list = array();

    $active = $this->productSorting->getActive();
    $active['url'] = replace_url_for('sort', implode('-', array($active['name'], $active['direction'])));
    foreach ($this->productSorting->getList() as $item)
    {
      if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) {
        //        $item['direction'] = 'asc' == $item['direction'] ? 'desc' : 'asc';
        continue;
      }
      $list[] = array_merge($item, array(
        'url' => replace_url_for('sort', implode('-', array($item['name'], $item['direction'])))
      ));
    }

    $this->setVar('list', $list, true);
    $this->setVar('active', $active, true);
  }

  /**
   * Executes list component
   *
   * @param myDoctrineCollection | array $list Коллекция товаров | массив ид товаров
   */
  public function executeList()
  {
    $this->view = (isset($this->view) && !empty($this->view)) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', 'line',))) {
      $this->view = 'compact';
    }
  }

  /**
   * Executes pagination component
   *
   * @param myDoctrinePager $pager Листалка товаров
   * @deprecated
   */
  public function executePagination()
  {
    if (!$this->pager->haveToPaginate()) {
      return sfView::NONE;
    }
  }

  /**
   * Executes property component
   *
   * @param Product $product Товар
   * @param string $view Вид
   * @deprecated
   */
  public function executeProperty()
  {
    if (!in_array($this->view, array('default', 'inlist'))) {
      $this->view = 'default';
    }

    $list = array();
    if (isset($this->product['Parameter'])) foreach ($this->product['Parameter'] as $parameter)
    {
      $value = $parameter->getValue();

      if (empty($value)) continue;

      if ('inlist' == $this->view && !$parameter->isViewList()) continue;

      $list[] = array(
        'name' => $parameter->getName(),
        'value' => $value,
      );
    }

    $this->setVar('list', $list, true);
  }

  /**
   * Executes list_view component
   * @deprecated
   */
  public function executeList_ajax_view()
  {
    $this->executeList_view();
  }

  /**
   * Executes list_view component
   *
   */
  public function executeList_view()
  {
    $this->view = $this->view ? : $this->getRequestParameter('view', 'compact');

    $list = array(
      array(
        'name' => 'compact',
        'title' => 'компактный',
        'class' => 'tableview',
      ),
      array(
        'name' => 'expanded',
        'title' => 'расширенный',
        'class' => 'listview',
      ),
    );

    foreach ($list as &$item)
    {
      $excluded = ($this->productCategory && ($item['name'] == $this->productCategory->product_view))
        ? array('view' => $item['name'])
        : null;

      $item = array_merge($item, array(
        'url' => replace_url_for('view', $item['name'], null, array(), $excluded),
        'current' => $this->view == $item['name'],
      ));
    }
    if (isset($item))
      unset($item);

    $this->setVar('list', $list, true);
  }

  /**
   * Executes f1_lightbox component
   *
   */
  public function executeF1_lightbox()
  {
    if ($this->parentAction == '_list_for_product_in_cart') {
      $showInCardButton = true;
    } else {
      $showInCardButton = false;
    }
    $this->setVar('showInCardButton', $showInCardButton);
  }
}
