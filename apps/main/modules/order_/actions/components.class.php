<?php

/**
 * order_ components.
 *
 * @package    enter
 * @subpackage order_
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class order_Components extends myComponents
{
  /**
   * Executes field_region_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   */
  public function executeField_region_id()
  {
    $this->displayValue = $this->getUser()->getRegion('name');

    //dump($this->getUser()->getRegion());
    $this->setVar('region', $this->getUser()->getRegion('region'));
  }
  /**
   * Executes field_delivery_type_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   * @param myDoctrineCollection $deliveryTypeList Коллекция типов доставки для пользователя (с учетом региона и товаров в корзине)
   */
  public function executeField_delivery_type_id()
  {
    $choices = array();
    foreach ($this->deliveryTypes as $deliveryType)
    {
      /* @var $deliveryType DeliveryTypeEntity */

      $choices[$deliveryType->getId()] = array(
        'id'          => strtr($this->name, array('[' => '_', ']' => '_')).$deliveryType->getId(),
        'label'       => $deliveryType->getName(),
        'description' => $deliveryType->getDescription(),
        'type'        => $deliveryType->getToken(),
      );
    }

    $this->name = $this->name;
    $this->setVar('choices', $choices, true);
  }
  /**
   * Executes field_payment_method_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   */
  public function executeField_payment_method_id()
  {
    $sum =$this->getUser()->getCart()->getTotal();
    if ($sum < ProductEntity::MIN_CREDIT_PRICE) {
        $showCreditMethod = false;
    } else {
        $showCreditMethod = true;
    }
    $paymentMethodList = RepositoryManager::getPaymentMethod()->getList();
    foreach($paymentMethodList as $key => $method){
      if(!in_array($method->getId(), RepositoryManager::getPaymentMethod()->getAcceptedList())){
        unset($paymentMethodList[$key]);
      }
    }

    $selectedMethodId = 0;
    foreach ($paymentMethodList as $method) {
      if (empty($_COOKIE['credit_on']) || !$showCreditMethod) {
          $selectedMethodId  = $method->getId();
          break;
      } elseif ($method->isCredit()) {
          $selectedMethodId  = $method->getId();
          break;
      }
    }
    $this->setVar('showCreditMethod', $showCreditMethod, true);
    $this->setVar('selectedMethodId', $selectedMethodId, true);
    $this->setVar('paymentMethodList', $paymentMethodList, true);

    $creditBankList = RepositoryManager::getCreditBank()->getList();

    $this->setVar('creditBankList', $creditBankList, true);
    $jsonBankData = array();
    foreach ($creditBankList as $bank) {
        $jsonBankData[$bank->getId()]['name'] = $bank->getName();
        $jsonBankData[$bank->getId()]['href'] = $bank->getHref();
    }
    $this->setVar('bankJson', json_encode($jsonBankData), true);

    $dataForCredit = $this->getProductsDataForCredit();
    $this->setVar('dataForCredit', json_encode($dataForCredit));


  }
  /**
   * Executes field_products component
   *
   */
  public function executeField_products()
  {
    date_default_timezone_set('Europe/Moscow');

    $maxWeek = 4;

    $dates = array();
    $now = time();
    $time = 1 == date('w') ? $now : strtotime('Last Monday', $now);
    foreach (range(1, 7 * $maxWeek) as $i)
    {
      $dayDiff = intval(floor(($time - $now) / 86400));

      $prefix = '';
      if (0 == $dayDiff)
      {
        $prefix = 'сегодня ';
      }
      if (1 == $dayDiff)
      {
        $prefix = 'завтра ';
      }
      if (2 == $dayDiff)
      {
        $prefix = 'послезавтра ';
      }

      $dates[] = array(
        'value'        => date('Y-m-d', $time),
        'day'          => date('j', $time),
        'dayOfWeek'    => format_date($time, 'EEE', 'ru'),
        //'name'      => $prefix.date('Y-m-d', $time),
        'displayValue' => format_date($time, 'd MMMM', 'ru'),
        'weekNum'      => floor(($i - 1) / 7) + 1,
      );

      // icrement day
      $time = strtotime("+1 day", $time);
    }

    $this->setVar('dates', $dates, true);
  }

  function executeSeo_admitad() {

    $data = array();

    foreach ($this->orders as $order)
    {
      $products = RepositoryManager::getProduct()->getListById(array_map(function($i) {
        return $i['product_id'];
      }, $order['product']), true);

      $productsById = array();
      foreach ($products as $product)
      {
        /* @var $product ProductEntity */
        $productsById[$product->getId()] = $product;
      }

      foreach ($order['product'] as $productData) {
        if (!array_key_exists($productData['product_id'], $productsById)) continue;

        /* @var $category ProductEntity */
        $product = $productsById[$productData['product_id']];

        /* @var $category ProductCategoryEntity */
        $category = array_shift($product->getCategoryList());
        if (!$category instanceof ProductCategoryEntity) continue;

        if (!array_key_exists($category->getId(), $data))
        {
          $data[$category->getId()] = array(
            'sum'    => 0,
            'number' => '',
          );
        }

        $data[$category->getId()]['sum'] += $productData['price'] * $productData['quantity'];
        $data[$category->getId()]['number'] = $order['number'].'-'.$category->getId();
      }
    }

    $uid = $this->getRequest()->getCookie(sfConfig::get('app_admitad_cookie_name', 'admitad_uid'));
    if(!$uid || strlen($uid) != 32){
      $uid = false;
    }

    if ($uid) {
      $data['uid'] = $uid;
    } else {
      $data['uid'] = '';
    }

    //dump($data, 1);
    $this->setVar('data', $data, true);
  }

  function executeErrors()
  {
    $errors = $this->errors;

    if (empty($errors)) return sfView::NONE;

    foreach ($errors as &$error)
    {
      if (isset($error['id']))
      {
        /* @var $product ProductEntity */
        $product = array_shift(RepositoryManager::getProduct()->getListById(array($error['id']), true));

        $error['product'] = array();
        $error['product']['id'] = $product->getId();
        $error['product']['token'] = $product->getToken();
        $error['product']['name'] = $product->getName();
        $error['product']['image'] = $product->getMediaImageUrl(0);

        $productCartInfo = $this->getUser()->getCart()->getProduct($error['id']);
        if($productCartInfo){
          /** @var $productCartInfo light\ProductCartData */

          $error['product']['quantity'] = $productCartInfo->getQuantity();
          $error['product']['price'] = $productCartInfo->getPrice();
        }
        else{
          $error['product']['quantity'] = 0;
          $error['product']['price'] = 0;
        }

        if (!empty($error['quantity_available']))
        {
          $error['product']['addUrl'] = $this->generateUrl('cart_add', array('product' => $product->getId(), 'quantity' => $error['quantity_available']));
        }
        $error['product']['deleteUrl'] = $this->generateUrl('cart_delete', array('product' => $product->getId()));
      }

      if (708 == $error['code'])
      {
        $error['message'] = !empty($error['quantity_available']) ? "Доступно только {$error['quantity_available']} шт." : $error['message'];
      }

    } if (isset($error)) unset($error);

    //dump($errors);

    $this->setVar('errors', $errors, true);
  }

  /**
   * Executes list component
   *
   */
  public function executeList()
  {

    $list = RepositoryManager::getOrder()->getByUser($this->getUser()->getGuardUser()->core_id);

    $productIds = array();
    $serviceIds = array();

    foreach($list as $order){
      /** @var $order OrderEntity */
      $items = $order->getItem();
//      echo 'order: '.$order->getNumber()."<br/>\r\n";
      foreach($items as $item){
        /** @var $item OrderItemEntity */
        if($item->getServiceId()){
//          echo 'service: <pre>'; var_export($item); echo '</pre>';
          $serviceIds[] = $item->getServiceId();
        }
        else{
//          echo 'product: <pre>'; var_export($item); echo '</pre>';
          $productIds[] = $item->getProductId();
        }
      }
    }

    $productIds = array_unique($productIds);
    $serviceIds = array_unique($serviceIds);

    $productList = array();
    $serviceList = array();

    $prodCb = function($data) use(&$productList){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        $productList[$product->getId()] = $product;
      }
    };

    $serviceCb = function($data) use(&$serviceList){
      /** @var $data ServiceEntity[] */

      foreach($data as $serviceCoreInfo){
        $serviceList[$serviceCoreInfo->getId()] = $serviceCoreInfo;
      }
    };
    if(count($productIds)){
      RepositoryManager::getProduct()->getListByIdAsync($prodCb, $productIds);
    }
    if(count($serviceIds)){
      RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIds);
    }

    if(count($serviceIds) || count($productIds)){
      CoreClient::getInstance()->execute();
    }

    $deliveryTypes = array();
    foreach(RepositoryManager::getDeliveryType()->getList() as $val){
      $deliveryTypes[$val->getId()] = $val;
    }

    $paymentMethods = array();
    foreach(RepositoryManager::getPaymentMethod()->getList() as $val){
      $paymentMethods[$val->getId()] = $val;
    }

    $this->setVar('list', $list, true);
    $this->setVar('productList', $productList, true);
    $this->setVar('serviceList', $serviceList, true);
    $this->setVar('statusList', RepositoryManager::getOrder()->getStatusList(), true);
    $this->setVar('deliveryTypeList', $deliveryTypes, true);
    $this->setVar('deliveryTypeList', $deliveryTypes, true);
    $this->setVar('paymentMethodList', $paymentMethods, true);
  }

  private function getProductsDataForCredit(){
    $productList = $this->getUser()->getCart()->getProducts();
    $prodIdList = array_keys($productList);
    $list = array();

    $prodCb = function($data) use(&$list, $productList){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $productList[$product->getId()];

        $rootCategoryToken = '';

        foreach($product->getCategoryList() as $category){
          if($category->getLevel() == 1){
            $rootCategoryToken = $category->getToken();
            break;
          }
        }

        $list[] = array(
          'id' => $product->getId(),
          'quantity' => $cartInfo->getQuantity(),
          'price' => $cartInfo->getPrice(),
          'type' => CreditBankRepository::getCreditTypeByCategoryToken($rootCategoryToken),
        );
      }
    };

    RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    CoreClient::getInstance()->execute();

    return $list;
  }
}

