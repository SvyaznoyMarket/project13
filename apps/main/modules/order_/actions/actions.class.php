<?php

/**
 * order_ actions.
 *
 * @package    enter
 * @subpackage order_
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class order_Actions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }

  /**
   * Executes new action
   *
   * @param sfRequest $request A request object
   */
  public function executeNew(sfWebRequest $request)
  {
    /* @var myUser */
    $user = $this->getUser();

    if (true
      && (0 == count($user->getCart()->getProducts()))
      && (0 == count($user->getCart()->getServices()))
    ) {
      $this->redirect('cart');
    }

    $this->order = new Order();
    $this->order->region_id = $this->getUser()->getRegion('id');
    $this->form = $this->getOrderForm($this->order);
  }

  /**
   * Executes deliveryMap action
   *
   * @param sfRequest $request A request object
   */
  public function executeDeliveryMap(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    /* @var myUser */
    $user = $this->getUser();

    $deliveryType = $request['delivery_type_id'] ? DeliveryTypeTable::getInstance()->getById($request['delivery_type_id']) : null;
    $this->forward404Unless($deliveryType);

    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $product)
    {
      $productsInCart[] = array('id' => $product['core_id'], 'quantity' => $product['cart']['quantity']);
    }

    $servicesInCart = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      // если услуга принадлежит товару, то пропустить
      if (count($service['cart']['product'])) continue;

      $servicesInCart[] = array('id' => $service['core_id'], 'quantity' => $service['cart']['quantity']);
    }

    //$result = json_decode(file_get_contents(__DIR__.'/../data/order-calc.json'), true);
    $result = Core::getInstance()->getDeliveryMap(
      $user->getRegion('core_id'),
      $productsInCart,
      $servicesInCart,
      'self' == $deliveryType->token ? ($deliveryType->token.'_24') : $deliveryType->token
    );
    $deliveryMap = $result;
    foreach($deliveryMap['products'] as &$productData)
    {
      $product = ProductTable::getInstance()->createQuery()->where('core_id = ?', $productData['id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

      $productData['image'] = $productData['media_image'];
      unset($productData['media_image']);

      $productData['cost'] = $productData['price'] * $productData['quantity'];
      $productData['delete_url'] = $this->generateUrl('cart_delete', array('product' => $product['token_prefix'].'/'.$product['token']));
    } if (isset($productData)) unset($productData);
    //$deliveryMap = array();
    //myDebug::dump($deliveryMap, 1);

    return $this->renderJson(array(
      'success' => true,
      'data'    => $deliveryMap,
    ));
  }

  /**
   * Executes create action
   *
   * @param sfRequest $request A request object
   */
  public function executeCreate(sfWebRequest $request)
  {
    /* @var myUser */
    $user = $this->getUser();


  }


  private function getOrderForm($order = null)
  {
    /* @var $user myUser */
    $user = $this->getUser();

    $regions = RegionTable::getInstance()->getListForOrder($this->getUser()->getCart()->getProducts()->toValueArray('id'));

    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $product)
    {
      $productsInCart[] = array('id' => $product['core_id'], 'quantity' => $product['cart']['quantity']);
    }

    $servicesInCart = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      // если услуга принадлежит товару, то пропустить
      if (count($service['cart']['product'])) continue;

      $servicesInCart[] = array('id' => $service['core_id'], 'quantity' => $service['cart']['quantity']);
    }

    $deliveryTypes = array();
    $result = Core::getInstance()->query('order.calc-delivery', array(), array(
      'geo_id'  => $user->getRegion('core_id'),
      'product' => $productsInCart,
      'service' => $servicesInCart,
    ));
    foreach ($result as $k => $item)
    {
      if ('unavailable' == $k) continue;

      $deliveryData = DeliveryTypeTable::getInstance()->createQuery()->select('name, description')->where('token = ?', $item['token'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
      $item['name'] = $deliveryData['name'];
      $item['desc'] = $deliveryData['description'];
      $deliveryTypes[] = RepositoryManager::getDeliveryType()->create($item);
    }
    //myDebug::dump($deliveryTypes, 1);

    // если нет регионов или способов доставки
    if (!count($regions) || !count($deliveryTypes))
    {
      $this->redirect('cart');
    }

    return new OrderDefaultForm($order, array('user' => $this->getUser(), 'regions' => $regions, 'deliveryTypes' => $deliveryTypes));
  }
}
