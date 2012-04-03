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

    $deliveryTypes = $this->form->getOption('deliveryTypes');
    $defaultDeliveryType = (1 == count($deliveryTypes)) ? $deliveryTypes[0] : null;
    $deliveryMap = $this->getDeliveryMapView($defaultDeliveryType);

    $this->setVar('deliveryMap', $deliveryMap, true);
    $this->setVar('mapCenter', json_encode(array('latitude' => $user->getRegion('latitude'), 'longitude' => $user->getRegion('longitude'))));
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

    $shop = $request['shop_id'] ? ShopTable::getInstance()->getById($request['shop_id']) : null;

    $deliveryMap = $this->getDeliveryMapView($deliveryType, $shop);

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

  private function getDeliveryMapView($deliveryType = null, $shop = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('Date');

    /* @var myUser */
    $user = $this->getUser();

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
      $deliveryType ? $deliveryType->getToken() : null
    );
    //myDebug::dump($result, 1);

    $deliveryMapView = new Order_DeliveryMapView();

    foreach ($result['shops'] as $coreData)
    {
      $shopView = new Order_ShopView();
      $shopView->address = $coreData['address'];
      $shopView->id = $coreData['id'];
      $shopView->latitude = $coreData['coord_lat'];
      $shopView->longitude = $coreData['coord_long'];
      $shopView->name = $coreData['name'];
      $shopView->regime = $coreData['working_time'];

      $deliveryMapView->shops[$shopView->id] = $shopView;
    }

    // сборка товаров
    foreach ($result['products'] as $coreData)
    {
      $recordData = ProductTable::getInstance()->createQuery()->where('core_id = ?', $coreData['id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
      $cartData = $user->getCart()->getProduct($recordData['id'])->cart;

      $itemView = new Order_ItemView();
      $itemView->deleteUrl = $this->generateUrl('cart_delete', array('product' => $recordData['token_prefix'].'/'.$recordData['token']));
      $itemView->id = $coreData['id'];
      $itemView->name = $coreData['name'];
      //$itemView->image = ProductTable::getInstance()->getMainPhotoUrl($recordData, 0);
      $itemView->image = $coreData['media_image'];
      $itemView->price = $coreData['price'];
      $itemView->quantity = $cartData['quantity'];
      $itemView->total = $cartData['total'];
      $itemView->totalFormatted = $cartData['formatted_total'];
      $itemView->type = Order_ItemView::TYPE_PRODUCT;
      $itemView->url = $this->generateUrl('productCard', array('product' => $recordData['token_prefix'].'/'.$recordData['token']));
      $itemView->token = $itemView->type.'-'.$itemView->id;

      foreach ($coreData['deliveries'] as $deliveryToken => $deliveryData)
      {
        $deliveryView = new Order_DeliveryView();
        $deliveryView->price = $deliveryData['price'];
        $deliveryView->token = $deliveryToken;
        $deliveryView->name = 0 === strpos($deliveryToken, 'self') ? 'В самовывоз' : 'В доставку';
        foreach ($deliveryData['dates'] as $dateData)
        {
          $dateView = new Order_DateView();
          $dateView->day = date('j', strtotime($dateData['date']));
          $dateView->dayOfWeek = format_date($dateData['date'], 'EEE', 'ru');
          $dateView->value = date('Y-m-d', strtotime($dateData['date']));
          foreach ($dateData['interval'] as $intervalData)
          {
            $intervalView = new Order_IntervalView();
            $intervalView->start_at = $intervalData['time_begin'];
            $intervalView->end_at = $intervalData['time_end'];

            $dateView->intervals[] = $intervalView;
          }

          $deliveryView->dates[] = $dateView;
        }

        $itemView->deliveries[$deliveryView->token] = $deliveryView;
      }

      $deliveryMapView->items[$itemView->token] = $itemView;
    }

    // сборка типов доставки
    foreach ($result['deliveries'] as $deliveryTypeToken => $coreData)
    {
      $recordData = DeliveryTypeTable::getInstance()->createQuery()->where('token = ?', $coreData['token'])->fetchOne(array(), Doctrine::HYDRATE_ARRAY);

      $deliveryTypeView = new Order_DeliveryTypeView();
      $deliveryTypeView->description = $recordData['description'];
      $deliveryTypeView->id = $coreData['mode_id'];
      $deliveryTypeView->name = $recordData['name'];
      $deliveryTypeView->type = $recordData['token'];
      $deliveryTypeView->token = $deliveryTypeToken;
      $deliveryTypeView->shortName = 0 === strpos($deliveryTypeView->type, 'self') ? 'Самовывоз' : 'Доставим';

      $deliveryTypeView->shop =
        array_key_exists($coreData['shop_id'], $deliveryMapView->shops)
          ? $deliveryMapView->shops[$coreData['shop_id']]
          : null
      ;

      foreach ($deliveryMapView->items as $itemView)
      {
        if (!in_array($itemView->id, $coreData['products'])) continue;

        $deliveryTypeView->items[] = $itemView->token;
      }

      $tmpDates = null;
      $dates = array();
      foreach ($deliveryTypeView->items as $itemToken)
      {
        $dates = array_map(function($i) { return $i->value; }, $deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates);
        $dates = is_array($tmpDates) ? array_intersect($dates, $tmpDates) : $dates;
        $tmpDates = $dates;
      }
      $deliveryTypeView->date = array_shift($dates);
      $deliveryTypeView->displayDate = format_date($deliveryTypeView->date, 'd MMMM', 'ru');

      $interval =
        isset($deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0])
        ? $deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]
        : null
      ;
      $deliveryTypeView->interval = ($interval) ? ($interval->start_at.','.$interval->end_at) : null;
      $deliveryTypeView->displayInterval = ($interval) ? ('с '.$interval->start_at.' по '.$interval->end_at) : null;

      $deliveryMapView->deliveryTypes[$deliveryTypeView->token] = $deliveryTypeView;
    }

    foreach ($deliveryMapView->items as $itemView)
    {
      foreach ($itemView->deliveries as $deliveryView)
      {
        $deliveryView->name .= ''
          .(
            $deliveryMapView->deliveryTypes[$deliveryView->token]->shop
            ? (' '.str_replace('г. Москва,', '', $deliveryMapView->deliveryTypes[$deliveryView->token]->shop->address))
            : ''
          )
        ;
      }
    }

    //var_dump($deliveryMapView); die();

    return $deliveryMapView;
  }
}
