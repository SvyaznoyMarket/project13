<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 22.06.12
 * Time: 12:30
 * To change this template use File | Settings | File Templates.
 */

require_once(sfConfig::get('sf_root_dir').'/light/lib/cart/Cart.php');
require_once(sfConfig::get('sf_root_dir').'/light/lib/cart/SessionCartContainer.php');
require_once(sfConfig::get('sf_root_dir').'/light/lib/cart/SymfonyCartPriceContainer.php');



class UserCartNew
{

  private $cart;

  public function __construct($parameters = array()){
    $this->cart = new \light\Cart(new \light\SessionCartContainer(), new SymfonyCartPriceContainer());
  }

  // Used only in /orders/create-external (@TODO move to plainPhp)
  public function addProduct($id, $qty = 1)
  {
    $this->cart->addProduct((int)$id, (int)$qty);
  }

  // Unused
   public function addService($serviceId, $quantity = 1, $productId = 0)
   {
     $this->cart->addService((int)$serviceId, (int)$quantity, (int)$productId);
   }

  /**
   * @param int $id
   * @return light\ProductCartData|null
   */
  public function getProduct($id)
  {
    try{
      $product = $this->cart->getProduct((int)$id);

      if(!$product){
        return Null;
      }

      return $product;
//      return array('id' => $product->getProductId(), 'price' => $product->getPrice(), 'quantity' => $product->getQuantity());
    }
    catch(Exception $e){
      sfContext::getInstance()->getLogger()->err($e->getMessage());
      return Null;
    }
  }

  public function getService($id)
  {
    try{
      $serviceList = $this->cart->getServiceList();

      if(array_key_exists($id, $serviceList)){
        $serviceList = $serviceList[$id];
      }
      else{
        return Null;
      }

      $return = array(
        'id' => $id,
        'products' => array()
      );

      foreach($serviceList as $productId => $service){
        /** @var $service light\ServiceCartData */
        $return['products'][$productId] = array(
          'quantity' => $service->getQuantity(),
          'price' => $service->getPrice()
        );
      }

      return $return;
    }
    catch(Exception $e){
      sfContext::getInstance()->getLogger()->err($e->getMessage());
      return Null;
    }
  }

  // Used only in cart component show() method
  public function getServicesByProductId($productId)
  {
    try{
      $serviceList = $this->cart->getServiceList();

      $return = array();

      foreach($serviceList as $serviceId => $service){
        if(array_key_exists($productId, $service)){
          $return[$serviceId] = $service;
        }
      }
      return $return;
    }
    catch(Exception $e){
      sfContext::getInstance()->getLogger()->err($e->getMessage());
      return array();
    }
  }

  // Unused
  public function deleteProduct($id)
  {
    $this->cart->removeProduct((int)$id);
  }

  // Unused
  public function deleteService($id, $productId = 0)
  {
    $this->cart->removeService((int)$id, (int)$productId);
  }

  // Used in order & order_
  public function clear()
  {
    $this->cart->clear();
  }

  public function hasProduct($id)
  {
    try{
      $product = $this->cart->getProduct((int)$id);

      return (bool) $product;
    }
    catch(Exception $e){
      return false;
    }
  }

  // legacy Copy-past
  public function getWeight()
  {

  }

  public function getDeliveriesPrice()
  {
    $dProducts = $this->cart->getProductsQuantities();

    if(!count($dProducts)){
      return array();
    }

    $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
      'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
      'product' => $dProducts
    ));
    if (!$deliveries || !count($deliveries) || isset($deliveries['result']))
    {
      $deliveries = array(array(
        'mode_id' => 1,
        'date' => date('Y-m-d', time() + (3600 * 48)),
        'price' => null,
      ));
    }
    $result = array();
    foreach ($deliveries as $d)
    {
      $result[$d['mode_id']] = $d['price'];
    }
    return $result;
  }

  public function getTotal($is_formatted = false)
  {
    $total = $this->cart->getTotalPrice();
    $result = $is_formatted ? number_format($total, 0, ',', ' ') : $total;
    return $result;
  }

  public function getProductsPrice(){
    $productsList = $this->cart->getProductList();
    $total = 0;
    foreach($productsList as $product){
      $total += $product->getTotalPrice();
    }
    return $total;
  }

  public function getQuantityById($id)
  {
    $products = $this->cart->getProductsQuantities();

    foreach ($products as $product){
      if($product['id'] == $id){
        return $product['quantity'];
      }
    }
    return 0;
  }

  /**
   * @return \light\ProductCartData[] key - productId
   */
  public function getProducts()
  {
    return $this->cart->getProductList();
  }

  /**
   * * @return array()   array('serviceId' => array('productId'=> \light\ServiceCartData))
   */
  public function getServices()
  {
    return $this->cart->getServiceList();
  }

  public function getWarranties()
  {
    return $this->cart->getWarrantyList();
  }

  public function getWarrantyByProduct($productId) {
    foreach ($this->getWarranties() as $warrantiesByProduct) {
      if (!array_key_exists($productId, $warrantiesByProduct)) continue;

      return $warrantiesByProduct[$productId];
    }

    return null;
  }

  public function count()
  {
    return $this->cart->getTotalQuantity();
  }

  public function countFull()
  {
    return $this->count();
  }

  public function getSeoCartArticle()
  {
    $orderArticleAR = array();

    $productList = $this->cart->getProductsQuantities();

    foreach ($productList as $product) {
      $orderArticleAR[] = $product['id'];
    }
    $orderArticle = implode(',', $orderArticleAR);
    return $orderArticle;
  }

}
