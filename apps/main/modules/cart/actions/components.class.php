<?php

/**
 * creator components.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cartComponents extends myComponents
{
  /**
   * Executes buy_button component
   *
   * @param Product $product Товар
   * @param int $quantity Количество товара
   * @param string $view Вид отображения кнопки
   */
  public function executeBuy_button()
  {
    if (empty($this->quantity)) {
      $this->quantity = 1;
    }
    $cart = $this->getUser()->getCart();
    $this->disable = false;
    $productPath = '';
    if (is_object($this->product)) {
      if (!$this->product->is_insale) {
        $this->disable = true;
      }
      $productId = $this->product->id;
      if (property_exists($this->product, 'path')) {
        $productPath = $this->product->token;
      }
    } else {
      if (!$this->product['is_insale']) {
        $this->disable = true;
      }
      $productId = $this->product['id'];
      $productPath = $this->product['token'];
    }
    $hasProduct = $cart->hasProduct($productId);
    $this->setVar('productPath', $productPath);
    $this->setVar('productId', $productId);


    if ($hasProduct) {
      $this->button = 'cart';
    }
    else
    {
      $this->button = 'buy';
    }
    //myDebug::dump($this->button);

    if ($this->soa == true) {
      $this->view = 'soa';
    } elseif (!in_array($this->view, array('default', 'delivery'))) {
      $this->view = 'default';
    }

  }


  /**
   * Executes show component
   *
   */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'order'))) {
      $this->view = 'default';
    }
    $cart = $this->getUser()->getCart();

    if ($this->view == 'order') {
      $list = $this->getReceiptList($cart);
    }
    else
    {
      $list = $this->getProductServiceList($cart);
    }

    $this->setVar('list', $list, true);

    $dataForCredit = array();

    foreach($list as $product){
      if($product['type'] == 'product'){
        $dataForCredit[] = array(
          'id' => $product['id'],
          'quantity' => $product['quantity'],
          'price' => $product['price'],
          'type' => CreditBankRepository::getCreditTypeByCategoryToken($product['main_category_token']),
        );
      }
    }

    $this->setVar('dataForCredit', json_encode($dataForCredit));
  }

  private function getCreditData($cart){

  }

  /**
   * @param UserCartNew $cart
   * @return array
   */
  private function getReceiptList($cart){

    $prods = $cart->getProducts();
    $services = $cart->getServices();

    $prodIdList = array_keys($prods);
    $serviceIdList = array_keys($services);

    $list = array();

    $prodCb = function($data) use(&$list, $prods){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $prods[$product->getId()];

        $mainCategory = $product->getCategoryList();
        $mainCategory = $mainCategory[0];

        $list[] = array(
          'type' => 'product',
          'id' => $product->getId(),
          'name' => $product->getName(),
          'token' => $product->getToken(),
          'token_prefix' => $product->getPrefix(),
          'quantity' => $cartInfo->getQuantity(),
          'price' => number_format($cartInfo->getPrice(), 0, ',', ' '),
          'main_category_token' => $mainCategory->getToken(),
        );
      }
    };

    $serviceCb = function($data) use(&$list, $services){
      /** @var $data ServiceEntity[] */

      foreach($data as $serviceCoreInfo){
        /** @var $cartInfo \light\ServiceCartData[]  array('productId' => ServiceCartData, 'productId' => ServiceCartData)*/
        $cartInfo = $services[$serviceCoreInfo->getId()];
        $qty = 0;
        $price = 0;

        foreach ($cartInfo as $prodId => $prodServInfo)
        {
          $qty += $prodServInfo->getQuantity();
          $price += $prodServInfo->getTotalPrice();
        }
        $list[] = array(
          'type' => 'service',
          'name' => $serviceCoreInfo->getName(),
          'token' => $serviceCoreInfo->getToken(),
          'quantity' => $qty,
          'price' => number_format($price, 0, ',', ' '),
        );
      }
    };
    RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIdList, true);
    CoreClient::getInstance()->execute();

    return $list;
  }

  /**
   * @param UserCartNew $cart
   * @return array
   */
  private function getProductServiceList($cart){

    $urls = sfConfig::get('app_product_photo_url');
    $urlsService = sfConfig::get('app_service_photo_url');

    $prods = $cart->getProducts();
    $services = $cart->getServices();

    $prodIdList = array_keys($prods);
    $serviceIdList = array_keys($services);

    $productList = array();
    $serviceList = array();

    $prodCb = function($data) use(&$productList, $prods, $urls){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $prods[$product->getId()];

        $mainCategory = $product->getCategoryList();
        $mainCategory = $mainCategory[0];

        $productList[$product->getId()] = array(
          'type' => 'product',
          'id' => $product->getId(),
          'core_id' => $product->getId(),
          'token_prefix' => $product->getPrefix(),
          'token' => $product->getToken(),
          'name' => $product->getNameWeb(),
          'quantity' => $cartInfo->getQuantity(),
          'service' => array(),
          'price' => $cartInfo->getPrice(),
          'priceFormatted' =>  number_format($cartInfo->getPrice(), 0, ',', ' '),
          'total' => number_format($cartInfo->getTotalPrice(), 0, ',', ' '),
          'photo' => $urls[1] . $product->getMediaImage(),
          'main_category_token' => $mainCategory->getToken(),
        );
      }
    };

    $serviceCb = function($data) use(&$serviceList, $services, $urlsService){
      /** @var $data ServiceEntity[] */

      foreach($data as $serviceCoreInfo){
        /** @var $cartInfo \light\ServiceCartData[]  array('productId' => ServiceCartData, 'productId' => ServiceCartData)*/

        if(!array_key_exists($serviceCoreInfo->getId(), $services)){
          sfContext::getInstance()->getLogger()->warning('core response contains service (id: "'.$serviceCoreInfo->getId().'") not contained in Cart ("'.json_enocde(array_keys($services)).'")');
          continue;
        }

        $cartInfo = $services[$serviceCoreInfo->getId()];

        if(!array_key_exists($serviceCoreInfo->getId(), $serviceList)){
          $serviceList[$serviceCoreInfo->getId()] = array();
        }

        foreach ($cartInfo as $prodId => $prodServInfo)
        {
          $serviceList[$serviceCoreInfo->getId()][$prodId] = array(
            'type'      => 'service',
            'id'        => $serviceCoreInfo->getId(),
            'core_id'   => $serviceCoreInfo->getId(),
            'token'     => $serviceCoreInfo->getToken(),
            'name'      => $serviceCoreInfo->getName(),
            'quantity'  => $prodServInfo->getQuantity(),
            'service'   => array(
              'id'        => $serviceCoreInfo->getId(),
              'token'     => $serviceCoreInfo->getToken(),
              'quantity'  => $prodServInfo->getQuantity(),
              'price'     => $prodServInfo->getPrice()),
            'price'     => $prodServInfo->getPrice(),
            'total'     => number_format($prodServInfo->getTotalPrice(), 0, ',', ' '),
            'priceFormatted'  => number_format($prodServInfo->getPrice(), 0, ',', ' '),
            'photo' => $urlsService[2] . $serviceCoreInfo->getMediaImage()
          );
        }
      }
    };
    RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIdList, true);
    CoreClient::getInstance()->execute();

    $list = array_values($productList);

    foreach ($serviceList as $serviceId => $services){
      foreach($services as $productId => $serviceArray){
        if(((int)$productId > 0) && array_key_exists($productId, $productList)){
          //Добавляем услугу к продукту
          foreach($list as $key => $val){
            if($val['id'] == $productId){
              $list[$key]['service'][] = $serviceArray;
              break;
            }
          }
        }
        else{
          // Услуга лежит отдельно
          $list[] = $serviceArray;
        }
      }
    }

    return $list;
  }
}
