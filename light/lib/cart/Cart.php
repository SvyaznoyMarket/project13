<?php
namespace light;
use InvalidArgumentException;
use Logger;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 07.06.12
 * Time: 14:15
 * To change this template use File | Settings | File Templates.
 */

require_once('interface/CartContainer.php');
require_once('interface/CartPriceContainer.php');
require_once('data/ProductCartData.php');
require_once('data/ServiceCartData.php');
require_once('data/WarrantyCartData.php');
require_once(__DIR__ . '/../log4php/Logger.php');

class Cart
{

  /**
   * @var CartContainer
   */
  private $dataContainer;

  /**
   * @var CartPriceContainer
   */
  private $priceContainer;

  /**
   * @var ProductCartData[]
   */
  private $productDataList = null;

  /**
   * @var ServiceCartData[] | null
   */
  private $serviceDataList = null;

  /**
   * @var WarrantyCartData[] | null
   */
  private $warrantyDataList = null;

  /**
   * @var null | float
   */
  private $totalPrice = null;

  /**
   * @param CartContainer $dataContainer
   */
  public function __construct(CartContainer $dataContainer, CartPriceContainer $priceContainer){
    $this->dataContainer = $dataContainer;
    $this->priceContainer = $priceContainer;
  }

  /**
   * @param int $productId
   * @param int $quantity
   */
  public function setProductQuantity($productId, $quantity){
    //@TODO добавить проверку наличия, когда на ядре реализуют этот функционал
    $this->dataContainer->setProductQuantity($productId, $quantity);
    $this->productDataList = null;
    $this->totalPrice = null;
  }

    public function addProduct($productId){
        //@TODO добавить проверку наличия, когда на ядре реализуют этот функционал
        $this->dataContainer->addProduct($productId);
        $this->productDataList = null;
        $this->totalPrice = null;
    }

  /**
   * @param int $productId
   * @param int|null $quantity
   */
  public function removeProduct($productId, $quantity=null){
    $productId = (int) $productId;
    $this->dataContainer->removeProduct($productId, $quantity);
    if(!is_null($this->productDataList) && array_key_exists($productId, $this->productDataList)){
      if($this->dataContainer->getProductQuantity($productId) > 0){
        $this->productDataList[$productId]->setQuantity($this->dataContainer->getProductQuantity($productId));
      }
      else{
        unset($this->productDataList[$productId]);
      }
    }
    $this->totalPrice = null;
  }

  /**
   * @param int $productId
   */
  public function removeProductServices($productId){
    $productId = (int)$productId;
    $list = $this->dataContainer->getServiceIdList($productId);

    foreach($list as $serviceId){
      $this->removeService($serviceId, null, $productId);
    }
  }

  /**
   * @param int $serviceId
   * @param int $quantity
   * @param int|null $productId
   */
  public function addService($serviceId, $quantity, $productId=null){
    //@TODO добавить проверку возможности, когда на ядре реализуют этот функционал
    $this->dataContainer->addService($serviceId, $quantity, $productId);
    $this->serviceDataList = null;
    $this->totalPrice = null;
  }

  public function setServiceQuantity($serviceId, $quantity, $productId=null){
      //@TODO добавить проверку возможности, когда на ядре реализуют этот функционал
      $this->dataContainer->setServiceQuantity($serviceId, $quantity, $productId);
      $this->serviceDataList = null;
      $this->totalPrice = null;
  }

  /**
   * @param int $serviceId
   * @param int|null $quantity
   * @param int $productId
   */
  public function removeService($serviceId, $quantity=null, $productId=0){
    $this->dataContainer->removeService($serviceId, $quantity, $productId);

    if(!is_null($this->serviceDataList) && array_key_exists($serviceId, $this->serviceDataList) && array_key_exists($productId, $this->serviceDataList[$serviceId])){
      $newQuantity = $this->dataContainer->getServiceQuantity($serviceId, $productId);
      if($newQuantity < 1){
        unset($this->serviceDataList[$serviceId][$productId]);
      }
      else{
        $this->serviceDataList[$serviceId][$productId]->setQuantity($newQuantity);
      }
    }
    $this->totalPrice = null;
  }

  /**
   * @param int $productId
   * @param int $quantity
   */
  public function setWarranty($warrantyId, $productId, $quantity = 1){
    $this->dataContainer->setWarranty($warrantyId, $productId, $quantity);
    $this->productWarrantyDataList = null;
    $this->totalPrice = null;
  }

  public function removeWarranty($warrantyId, $productId) {
    $this->dataContainer->removeWarranty($warrantyId, $productId);
    $this->productWarrantyDataList = null;
    $this->totalPrice = null;
  }

  /**
   * @return float
   */
  public function getTotalPrice(){
    if(is_null($this->totalPrice)){
      $this->fillData();
    }

    return $this->totalPrice;
  }

  /**
   * Очищает корзину
   */
  public function clear(){
    $this->dataContainer->clear();
    $this->productDataList = null;
    $this->serviceDataList = null;
    $this->totalPrice = null;
  }

  /**
   * Ф-я возвращает количество уникальных товаров в корзине
   * @return int
   */
  public function getProductsQuantity(){
    return $this->dataContainer->getProductsQuantity();
  }

  /**
   * @return array
   * array(
   *  array(
   *    'id' => 1,
   *    'quantity' => 1
   *  ),
   *  array(
   *    'id' => 2,
   *    'quantity' => 1
   *  )
   * )
   */
  public function getProductsQuantities(){
    return $this->dataContainer->getProductsQuantities();
  }

  /**
   * Функция возвращает сумму quantity всех услуг и товаров
   * @return int
   */
  public function getTotalQuantity(){
    return $this->dataContainer->getTotalQuantity();
  }

  /**
   * Ф-я возвращает количество уникальных умлуг в корзине
   * @param int|null $productId //Если null - считается во всех услугах, иначе - только для определенного продукта
   * @return int
   */
  public function getServicesQuantity($productId=null){
    return $this->dataContainer->getServicesQuantity($productId);
  }

  public function getWarrantiesQuantities() {
    return $this->dataContainer->getWarrantiesQuantity();
  }

  /**
   * @return ProductCartData[] key - productId
   */
  public function getProductList(){
    if(is_null($this->productDataList)){
      $this->fillData();
    }
    return $this->productDataList;
  }

  /**
   * @return WarrantyCartData[] key - warrantyId
   */
  public function getWarrantyList(){
    if(is_null($this->warrantyDataList)){
      $this->fillData();
    }
    return $this->warrantyDataList;
  }

  /**
   * @param int $id
   * @return ProductCartData|null
   */
  public function getProduct($id){
    if(is_null($this->productDataList)){
      $this->fillData();
    }

    $id = (int) $id;

    if(!array_key_exists($id, $this->productDataList)){
      $logger = \Logger::getLogger('Cart');
      \LoggerNDC::push('getProduct');
      $logger->error('Product with id "' . $id . '" not found');
      \LoggerNDC::pop();
      return null;
    }
    return $this->productDataList[$id];
  }

  /**
   * @param int $id если равно 0 - то возвращаем сервис без привязки к продукту
   * @param int $productId
   * @return null | ServiceCartData
   */
  public function getService($id, $productId=0){
    if(is_null($this->serviceDataList)){
      $this->fillData();
    }

    $id = (int) $id;
    $productId = (int) $productId;

    $logger = \Logger::getLogger('Cart');
      \LoggerNDC::push('getService');
    if(!array_key_exists($id, $this->serviceDataList)){
      $logger->error('Service with id "' . $id . '" not found');
      return null;
    }
    if(!array_key_exists($productId, $this->serviceDataList[$id])){
      $logger->error('Product with id "' . $productId . '" not found');
      return null;
    }
    \LoggerNDC::pop();

    return $this->serviceDataList[$id][$productId];
  }

  /**
   * @param null | int $productId
   * @return array()   array('serviceId' => array('productId'=> ServiceCartData))
   */
  public function getServiceList($productId=null){
    if(is_null($this->serviceDataList)){
      $this->fillData();
    }

    if(is_null($productId)){
      return $this->serviceDataList;
    }

    $productId = (int) $productId;
    $return = array();

    foreach($this->serviceDataList as $serviceId => $serviceList){
      if(array_key_exists($productId, $serviceList)){
        $return[$serviceId] = $serviceList[$productId];
      }
    }

    return $return;
  }

  /**
   * @param int $productId
   * @return bool
   */
  public function containsProduct($productId){
    return in_array($productId, $this->dataContainer->getProductIdList());
  }

  /**
   * Наполняет $this->fullData информацией, обращаясь к моделям
   */
  private function fillData(){
    // получаем список цен
    $response = $this->priceContainer->getPrices($this->dataContainer);
    //var_dump($response); exit();

    $this->totalPrice = (array_key_exists('price_total', $response))? $response['price_total'] : 0;

    $this->productDataList = array();
    if(array_key_exists('product_list', $response)){
      foreach($response['product_list'] as $productInfo){
        $this->productDataList[$productInfo['id']] = new ProductCartData($productInfo);
      }
    }

    $this->serviceDataList = array();
    if(array_key_exists('service_list', $response)){
      foreach($response['service_list'] as $serviceInfo){
        if(!array_key_exists($serviceInfo['id'], $this->serviceDataList)){
          $this->serviceDataList[$serviceInfo['id']] = array();
        }
        $relatedProductId = (array_key_exists('product_id', $serviceInfo)) ? intval($serviceInfo['product_id']) : 0;
        $this->serviceDataList[$serviceInfo['id']][$relatedProductId] = new ServiceCartData($serviceInfo);
      }
    }

    $this->warrantyDataList = array();
    if(array_key_exists('warranty_list', $response)){
      foreach($response['warranty_list'] as $warrantyInfo){
        if(!array_key_exists($warrantyInfo['warranty_id'], $this->warrantyDataList)){
          $this->warrantyDataList[$warrantyInfo['warranty_id']] = array();
        }
        $relatedProductId = (array_key_exists('product_id', $warrantyInfo)) ? intval($warrantyInfo['product_id']) : 0;
        $this->warrantyDataList[$warrantyInfo['warranty_id']][$relatedProductId] = new WarrantyCartData($warrantyInfo);
      }
    }
    //var_dump($this->warrantyDataList); exit();
  }

}
