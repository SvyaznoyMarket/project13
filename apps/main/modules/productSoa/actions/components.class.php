<?php

/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productSoaComponents extends myComponents
{

  /**
   * Executes show component
   *
   * @param Product $product Товар
   * @param view $view Вид
   */
  public function executeShow()
  {

    if (!$this->product)
    {
      return sfView::NONE;
    }

    if (!in_array($this->view, array('default', 'expanded', 'compact', 'description', 'line', 'orderOneClick', 'stock', 'extra_compact')))
    {
      $this->view = 'default';
    }

    // cache key
    $cacheKey = in_array($this->view, array('compact', 'expanded')) && sfConfig::get('app_cache_enabled', false) ? $this->getCacheKey(array(
      'product' => is_scalar($this->product) ? $this->product : $this->product->id,
      'region'  => $this->getUser()->getRegion('id'),
      'view'    => $this->view,
      'i'       => $this->ii,
    )) : false;

    // checks for cached vars
    if ($cacheKey && $this->setCachedVars($cacheKey))
    {
      //myDebug::dump($this->getVarHolder()->getAll(), 1);
      return sfView::SUCCESS;
    }


    $cartItem = $this->getUser()->getCart()->getProduct($this->product->id);
    //myDebug::dump($cartItem);
    if ($cartItem) {
      if (is_object($this->product)) {
        $this->product->cart_quantity = isset($cartItem['quantity']) ? $cartItem['quantity'] : 0;
      }
    }

    $this->setVar('keys', ProductTable::getInstance()->getCacheEraserKeys($this->product, 'show', array('region' => $this->getUser()->getRegion('geoip_code'), )));

    // caches vars
    if ($cacheKey)
    {
      $this->cacheVars($cacheKey);
      $this->getCache()->addTag("product-{$this->product->id}", $cacheKey);
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
   */
  public function executePager()
  {
    $this->view = !empty($this->view) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', 'line')))
    {
      $this->view = 'compact';
    }

    $list = $this->pager->getResults();

    $this->setVar('list', $list, true);
  }

  /**
   * Executes sorting component
   *
   * @param array $productSorting Сортировка списка товаров
   */
  public function executeSorting()
  {
    $list = array();

    $active = $this->productSorting->getActive();
    $active['url'] = replace_url_for('sort', implode('-', array($active['name'], $active['direction'])));
    foreach ($this->productSorting->getList() as $item)
    {
      if ($active['name'] == $item['name'] && $active['direction'] == $item['direction'])
      {
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
    if (!in_array($this->view, array('expanded', 'compact', 'line', )))
    {
      $this->view = 'compact';
    }
  }

  /**
   * Executes pagination component
   *
   * @param myDoctrinePager $pager Листалка товаров
   */
  public function executePagination()
  {
    if (!$this->pager->haveToPaginate())
    {
      return sfView::NONE;
    }
  }

  /**
   * Executes property component
   *
   * @param Product $product Товар
   * @param string $view Вид
   */
  public function executeProperty()
  {
    if (!in_array($this->view, array('default', 'inlist')))
    {
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
   * Executes property_grouped component
   *
   * @param Product $product Товар
   */
  public function executeProperty_grouped()
  {
    if (!in_array($this->view, array('default', 'inlist')))
    {
      $this->view = 'default';
    }

    $list = array();

    foreach ($this->product->property_group as $group) {
        $list[$group['id']] = $group;
    }
     // print_r($this->product->property);
    foreach ($this->product->property as $prop) {
      if (!isset($prop['is_view_card']) || !$prop['is_view_card']) {
          //continue;
      }
      if (isset($prop['group_id']) && $prop['group_id']) {
        if (is_array($prop['option_id'])) {
           $valueAr = array();
           foreach ($prop['option_id'] as $option) {
               $valueAr[] = $option['value'];
           }
           $prop['value'] = implode(', ', $valueAr);
        } elseif ($prop['value'] == 'true') {
            $prop['value'] = 'да';
        } elseif ($prop['value'] == 'false') {
            $prop['value'] = 'нет';
        }
        if ($prop['unit']) {
            $prop['value'] .= ' ' . $prop['unit'];
        }
        $list[$prop['group_id']]['parameters'][] = $prop;
      }
    }
      foreach ($list as $key =>  $group) {
          if (!isset($group['parameters'])) {
              unset($list[$key]);
          }
      }
      //   print_r($list);
    $this->setVar('list', $list, true);
    $this->setVar('product', $this->product, true);
  }

  /**
   * Executes product_group component
   *
   * @param Product $product Товар
   */
  public function executeProduct_model()
  {
    if (!$this->product->model && !isset($this->product->model['property']))
    {
      return sfView::NONE;
    }
    $product = $this->product;
    $this->setVar('product', $this->product, true);

    //print_r($this->product->model['property']);
    $propIdList = array();
    foreach ($this->product->model['property'] as $prop) {
        $propIdList[] = $prop['id'];
    }
    foreach ($this->product->model['product'] as $prod) {
        foreach ($prod->property as $prop) {
            if (in_array($prop['id'], $propIdList)) {
                $prodPropValue[$prod->id][$prop['id']] = $prop['value'];
            }
        }
    }
//      print_r($prodPropValue);
//      die();
    foreach ($this->product->model['property'] as $prop) {
        $property = array(
            'id' => $prop['id'],
            'name' => $prop['name'],
            'is_image' => $prop['is_image'],
        );
        //print_r($this->product->model);
        $valueList = array();
        foreach ($this->product->model['product'] as $productModel) {
            foreach ($this->product->model['property'] as $prodProp) {
                if ($prodProp['id'] == $prop['id']) {
                   $value = $prodPropValue[$productModel->id][$prodProp['id']];
                   if ($product->id == $productModel->id) {
                        $property['current']['id'] = $productModel->id;
                        $property['current']['value'] = $value;
                        $property['current']['url'] = $this->generateUrl('productCardSoa', array('product' => str_replace('/product/', '', $productModel->link) ));
                   } //elseif (in_array($prodProp['value'], $valueList)) {
//                       continue;
//                   }
                   //foreach (['property'])
                   if (trim($value) == 'true') {
                       $value = 'да';
                   } elseif (trim($value) == 'false') {
                       $value = 'нет';
                   }
                   $prodProp['value'] = $value;
                   $property['products'][$prodProp['value']] = array(
                       'id' => $productModel->id,
                       'name' => $productModel->name,
                       'image' => $product::getMainPhotoUrlByMediaImage($productModel->media_image, 1),
                       'value' => $prodProp['value'],
                       'is_selected' =>  ($this->product->id == $productModel->id) ? 1 : 0,
                       'url' => $this->generateUrl('productCardSoa', array('product' => str_replace('/product/', '', $productModel->link) ))
                   );
                   $valueList[] = $prodProp['value'];
                }
            }
        }
        $properties[] = $property;
    }
    //print_r($properties);
    //die();
    $this->setVar('properties', $properties, true);
    return;



    if (!$this->product->is_model && !$this->product->model_id)
    {
      return sfView::NONE;
    }

    $properties = $this->product->getModelProperty();
    if (!count($properties))
    {
      return sfView::NONE;
    }

    //myDebug::dump($properties);
    $model_id = !empty($this->product->model_id) ? $this->product->model_id : $this->product->id;
    $q = ProductTable::getInstance()->createBaseQuery(array('with_model' => true, ))->addWhere('product.model_id = ? or product.id = ?', array($model_id, $model_id,));
    //добавляем учет товара, доступного к продаже
    $q->addWhere('IFNULL(productState.is_instock, product.is_instock) = ?', true);

    $product_ids = ProductTable::getInstance()->getIdsByQuery($q);

    if (empty($product_ids))
    {
      return sfView::NONE;
    }

    $q = ProductPropertyRelationTable::getInstance()->createBaseQuery();
    $products_properties = $this->product->getPropertyRelation();

    foreach ($properties as $property)
    {
      $query = clone $q;
      $query->addWhere('productPropertyRelation.property_id = ?', array($property->id,));
      $query->andWhereIn('productPropertyRelation.product_id', $product_ids);
      $query->distinct();
      $value_ids = ProductPropertyRelationTable::getInstance()->getIdsByQuery($query);
      $values = ProductPropertyRelationTable::getInstance()->createListByIds($value_ids, array('index' => array('productPropertyRelation' => 'id',)));
      foreach ($products_properties as $products_property)
      {
        if ($property->id == $products_property->property_id)
        {
          $values[$products_property->id]->mapValue('is_selected', true);
        }
      }
      //myDebug::dump($values);
      $value_to_map = array();
      foreach ($values as $id => $value)
      {
        if (!$value->product_id) continue;
        $product = ProductTable::getInstance()->getById($value->product_id, array('with_model' => true, ));
        if (!$product) continue;
        $realValue = $value->getRealValue();
        $value_to_map[$realValue]['id'] = $id;
        $value_to_map[$realValue]['url'] = $this->generateUrl('changeProduct', array_merge($this->product->toParams(), array('value' => $value['id'])));
        $value_to_map[$realValue]['parameter'] = new ProductParameter($property['ProductTypeRelation'][0], array($value, ));
        if (isset($values[$id]['is_selected']))
        {
          $value_to_map[$realValue]['is_selected'] = $values[$id]['is_selected'];
        }
        elseif (!isset($value_to_map[$realValue]['is_selected']))
        {
          $value_to_map[$realValue]['is_selected'] = 0;
        }
        if ($value_to_map[$realValue]['is_selected'])
        {
          $property->mapValue('current', $realValue);
        }
        if ($property->ProductModelRelation[0]->is_image)
        {
          $value_to_map[$realValue]['photo'] = $product->getMainPhotoUrl(1);
        }

      }
      ksort($value_to_map);
      $property->mapValue('values', $value_to_map);
    }
    if (!isset($property->current))
    {
      return sfView::NONE;
    }
    $this->setVar('properties', $properties, true);
  }

  /**
   * Executes list_view component
   *
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
    $list = array(
      array(
        'name'  => 'compact',
        'title' => 'компактный',
        'class' => 'tableview',
      ),
      array(
        'name'  => 'expanded',
        'title' => 'расширенный',
        'class' => 'listview',
      ),
    );

    foreach ($list as &$item)
    {
      $item = array_merge($item, array(
        'url' => replace_url_for('view', $item['name']),
        'current' => $this->getRequestParameter('view', 'compact') == $item['name'],
        ));
    } if (isset($item))
      unset($item);

    $this->setVar('list', $list, true);
  }
  /**
   * Executes tags component
   *
   */
  public function executeTags()
  {
    if (!$this->product instanceof ProductSoa)
    {
      return sfView::NONE;
    }

    $list = array();
    foreach ($this->product->tag as $tag)
    {
      $list[] = array(
        'token' => $tag['token'],
        'url'   => $this->generateUrl('tag_show', array('tag' => $tag['site_token'])),
        'name'  => $tag['name'],
      );
    }

    $this->count = count($list);
    if (0 == $this->count)
    {
      return sfView::NONE;
    }

    $this->setVar('list', $list);
    $this->limit = 6 < count($list) ? 6 : count($list);
  }
  /**
   * Executes f1_lightbox component
   *
   */
  public function executeF1_lightbox(){
      if ($this->parentAction == '_list_for_product_in_cart') {
          $showInCardButton = true;
      } else {
          $showInCardButton = false;
      }
      if (is_array($this->product)) {
          $prodId = $this->product['id'];
      } else {
          $prodId = $this->product->id;
      }
      $this->setVar('productId', $prodId, true);
      $this->setVar('showInCardButton', $showInCardButton);
  }

  public function executeKit()
  {
    $this->kit = $this->product->kit;
    //$q = ProductTable::getInstance()->getQueryByKit($this->product);
    //$this->productPager = $this->getPagerForArray($this->kit, 12, array());

    //$this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');

    $this->view = 'compact';
  }

    public function executeDelivery()
    {
        $delivery = array();
        if (isset($this->product->delivery[3])) {
         $inf = $this->product->delivery[3];
         $inf['mode'] = 3;
         $delivery[] = $inf;
        }
        if (isset($this->product->delivery[2])) {
            $inf = $this->product->delivery[2];
            $inf['mode'] = 2;
            $delivery[] = $inf;
        }
        if (isset($this->product->delivery[1])) {
            $inf = $this->product->delivery[1];
            $inf['mode'] = 1;
            $delivery[] = $inf;
        }

        $now = new DateTime();
        foreach ($delivery as & $item) {
            $minDeliveryDate = DateTime::createFromFormat('Y-m-d', $item['date'][0]['date']);
            $deliveryPeriod = $minDeliveryDate->diff($now)->days;
            if ($deliveryPeriod < 0) $deliveryPeriod = 0;
            $deliveryPeriod = myToolkit::fixDeliveryPeriod($item['mode'], $deliveryPeriod);
            $item['period'] = $deliveryPeriod;
            $item['deliveryText'] = myToolkit::formatDeliveryDate($item['period']);
        }
        //print_r($delivery);
        $this->setVar('delivery', $delivery);
    }
}
