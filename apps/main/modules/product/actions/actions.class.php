<?php

/**
 * product actions.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productActions extends myActions
{
  public function executeList(sfWebRequest $request)
  {
    //$tokens = is_array($request['products']) ? $request['products'] : explode(',', $request['products']);
    $ids = is_array($request['products']) ? $request['products'] : explode(',', $request['products']);

    //$this->productList = ProductTable::getInstance()->getListByTokens($tokens);
    $this->productList = ProductTable::getInstance()->getListByCoreIds($ids);
    $this->forward404Unless($this->productList->count());
  }

  public function executeSet(sfWebRequest $request)
  {
    $barcodes = is_array($request['products']) ? $request['products'] : explode(',', $request['products']);

    $this->productList = ProductTable::getInstance()->getListByBarcodes($barcodes, array(
      'with_properties' => true,
      'property_view'   => 'list',
    ));
    $this->forward404Unless($this->productList->count());

    $categoryIds = array();
    foreach ($this->productList as $product)
    {
      if (in_array($product->Category[0]['id'], $categoryIds)) continue;

      $categoryIds[] = $product->Category[0]['id'];
    }

    $this->setVar('productCategoryList', ProductCategoryTable::getInstance()->createListByIds($categoryIds), true);
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeDeliveryInfo(sfWebRequest $request)
  {
    $productIds = $request->getParameter('ids');
    $data = array();
    $now = new DateTime();
    foreach ($productIds as $productId) {
      $productObj = ProductTable::getInstance()->findOneByCoreId($productId);
      if (!$productObj || !$productObj instanceof Doctrine_Record) {
        continue;
      }
      /*if ($productObj->isKit()) {
        $setItems = ProductKitRelationTable::getInstance()->findByKitId($productObj->id);
        $setCoreIds = array();
        foreach ($setItems as $setItem) {
          $setCoreIds[] = $setItem->Part->core_id;
        }
        $deliveries = Core::getInstance()->getProductDeliveryData($productId, $this->getUser()->getRegion('core_id'), $setCoreIds);
      } else {*/
        $deliveries = Core::getInstance()->getProductDeliveryData($productId, $this->getUser()->getRegion('core_id'));
      //}
      $result = array('success' => true, 'deliveries' => array());
      if (!$deliveries || !count($deliveries) || isset($deliveries['result'])) {
        $deliveries = array(array(
          'mode_id' => 1,
          'date' => date('Y-m-d', time()+(3600*48)),
          'price' => null,
        ));
      }
      $deliveryData = null;
      foreach ($deliveries as $i => $delivery) {
        $deliveryObj = DeliveryTypeTable::getInstance()->findOneByCoreId($delivery['mode_id']);
        $minDeliveryDate = DateTime::createFromFormat('Y-m-d', $delivery['date']);
        $deliveryPeriod = $minDeliveryDate->diff($now)->days;
        if ($deliveryPeriod < 0) $deliveryPeriod = 0;
        $deliveryPeriod = myToolkit::fixDeliveryPeriod($delivery['mode_id'], $deliveryPeriod);
        if ($deliveryPeriod === false) continue;
        $delivery['period'] = $deliveryPeriod;
        $delivery['object'] = $deliveryObj->toArray(false);
        $delivery['text'] = myToolkit::formatDeliveryDate($deliveryPeriod);
        $result['deliveries'][] = $delivery;
        if ($delivery['mode_id'] == 1) {
          $deliveryData = $delivery;
        }
      }
      if ($deliveryData === null) {
        $deliveryData = reset($deliveries);
      }
      $result['delivery'] = $delivery;
      $data[$productId] = $result;
    }
    print_r($data);
    return $this->renderJson($data);
  }

 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $table = ProductTable::getInstance();

    $field = 'id';
    $id = $request['product'];
    foreach (array('id', 'token', 'core_id', 'barcode', 'article') as $v)
    {
      if (0 === strpos($request['product'], $v))
      {
        $field = $v;
        $id = preg_replace('/^'.$v.'/', '', $id);

        break;
      }
    }

    $this->product = ProductTable::getInstance()->findOneBy($field, $id);

    $this->redirect(array('sf_route' => 'productCard', 'sf_subject' => $this->product), 301);
  }

  public function executeChange(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
    if (!$this->product->is_model && !$this->product->model_id)
    {
      $this->redirect('productCard', $this->product);
    }
    $model_id = !empty($this->product->model_id) ? $this->product->model_id : $this->product->id;

    $produtPropertyRelation = ProductPropertyRelationTable::getInstance()->getById($this->getRequestParameter('value'));
    if (!$produtPropertyRelation)
    {
      $this->redirect('productCard', $this->product);
    }

    $property_id = $produtPropertyRelation->property_id;
    $new_value = $produtPropertyRelation->getRealValue();

    //myDebug::dump($property_id);
    //myDebug::dump($new_value);
    //$new_value = $this->getRequestParameter('value');
    //$property_id = $this->getRequestParameter('property');

    $q = ProductTable::getInstance()->createBaseQuery(array('with_model' => true, ))->addWhere('product.model_id = ? or product.id = ?', array($model_id, $model_id,));
    //добавляем учет товара, доступного к продаже
    $q->addWhere('IFNULL(productState.is_instock, product.is_instock) = ?', true);
    //Продукты в серии
    $product_ids = ProductTable::getInstance()->getIdsByQuery($q);

    if (1 == count($product_ids))
    {
      $this->redirect('productCard', $this->product);
    }
    //myDebug::dump($product_ids);
    //$q = ProductPropertyRelationTable::getInstance()->createBaseQuery();
    //$q = ProductPropertyRelationTable::getInstance()->createBaseQuery()->addWhere('productPropertyRelation.product_id = ?', array($this->product->id, ));
    //$products_properties = $this->product->getPropertyRelation();
    //свойства текущего продукта
    //$product_property_ids = ProductPropertyRelationTable::getInstance()->getIdsByQuery($q);
    //myDebug::dump($product_property_ids);

    //Свойства группы
    //$q = ProductGroupPropertyRelationTable::getInstance()->createBaseQuery()->innerJoin('productGroupPropertyRelation.Property property')->addWhere('productGroupPropertyRelation.product_group_id = ?', array($this->product->group_id, ))->select('productGroupPropertyRelation.product_property_id, property.*');
    $q = ProductPropertyTable::getInstance()->createBaseQuery()
      ->from('ProductProperty productProperty indexby productProperty.id')
      ->innerJoin('productProperty.ProductModelRelation productModelRelation')
      ->select('productProperty.*')
      ->addWhere('productModelRelation.product_id = ?', array($model_id, ));
    //$group_property_ids = ProductGroupPropertyRelationTable::getInstance()->getIdsByQuery($q);
    //свойства, которые различаются в группе
    $groups_properties = $q->fetchArray();
    //myDebug::dump($groups_properties, 1);

    $product = $this->product;
    $group_property_ids = array_keys($groups_properties);
    /*foreach ($groups_properties as $groups_property)
    {
      $group_property_ids[] = $groups_property['id'];
    }*/

    $old_properties = array();
    foreach ($product->getPropertyRelation() as $property)
    {
      if (in_array($property->property_id, $group_property_ids))
      {
        $old_properties[$property->property_id]['value'] = $property->getRealValue();
        $old_properties[$property->property_id]['type'] = $property->getProperty()->getType();
        $old_properties[$property->property_id]['is_multiple'] = $property->getProperty()->is_multiple;
      }
    }
    $old_properties[$property_id]['value'] = $new_value;
    //myDebug::dump($old_properties, 1);

    $q = ProductTable::getInstance()->createBaseQuery(array('with_model' => true, ));
    $q->innerJoin('product.PropertyRelation propertyRelation');
    //$q->addSelect('SUM(IF(id = ' . $product->id . ', 1, 0)) as sum_id');
    $if_condition = "";
    foreach ($old_properties as $id => $value)
    {
      $if_condition .= strlen($if_condition) ? " OR " : "";
      $if_condition .= "(propertyRelation.property_id=".$id." AND propertyRelation.";
      $field = "";
      switch ($value['type']):
        case 'string': case 'text':
          $field = 'value_'.$value['type'].'=';
        break;
        case 'integer': case 'float':
          $field = 'value_float=';
        break;
        case 'select':
          if (!$value['is_multiple'])
          {
            $field = 'option_id=';
          }
          else
          {
            $field = 'option_id=';
          }
        break;
        case 'boolean':
          $field = 'value_boolean=';
        break;
      endswitch;
      $if_condition .= $field."'".$value['value']."')";

      if ($id == $property_id)
      {
        //$q->addWhere('propertyRelation.'.$field.'?', array($value['value']));
        $is_changed_property_presents = "SUM(IF(propertyRelation.{$field}'{$value['value']}', 1, 0)) as changed_property_presents";
      }
    }
    $q->select("product.id, SUM(IF({$if_condition}, 1, 0)) as matches, {$is_changed_property_presents}");
    $q->addWhere('product.id IN ('.implode(', ', array_diff($product_ids, array($product->id,))).')');
    //$q->addWhere('');
    $q->groupBy('product.id');
    $q->orderBy('changed_property_presents desc, matches desc, score desc');
    $q->having('matches > 0');
    //myDebug::dump($q->getParams());
    //myDebug::dump($q->getSqlQuery(), 1);
    $matches = $q->fetchArray();

    //если не нашли новый товар, то остаемся в этом же
    $new_product = !empty($matches) ? ProductTable::getInstance()->getById($matches[0]['id'], array('with_model' => true, )) : $this->product;
    //myDebug::dump($q->fetchArray(), true);


    //throw new sfException('We don\'t need a redirection');
    $this->redirect('productCard', $new_product);
    //myDebug::dump($this->product);
    //$this->forward('productCard', 'show');
  }

  /**
   * Executes related action
   *
   * @param sfRequest $request A request object
   */
  public function executeRelated(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $this->page = $request->getParameter('page', 1);

    $this->product = $this->getRoute()->getObject();

    $criteria = new ProductRelatedCriteria();
    $criteria->setParent($this->product['core_id']);
    $criteria->setPager(new myPager($this->page, 5));
    $item['related'] = RepositoryManager::get('Product')->getRelated($criteria);
    $item['related_pager'] = $criteria->getPager();

    $this->setVar('item', $item, true);

    return $this->renderPartial($this->getModuleName().'/product_related_list');
  }

  /**
   * Executes accessory action
   *
   * @param sfRequest $request A request object
   */
  public function executeAccessory(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $this->page = $request->getParameter('page', 1);

    $this->product = $this->getRoute()->getObject();

    $criteria = new ProductRelatedCriteria();
    $criteria->setParent($this->product['core_id']);
    $criteria->setPager(new myPager($this->page, 5));
    $item['accessory'] = RepositoryManager::get('Product')->getAccessory($criteria);
    $item['accessory_pager'] = $criteria->getPager();

    $this->setVar('item', $item, true);

    return $this->renderPartial($this->getModuleName().'/product_accessory_list');
  }
}
