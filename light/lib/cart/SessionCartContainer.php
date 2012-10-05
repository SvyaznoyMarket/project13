<?php
namespace light;
use Logger;

require_once('interface/CartContainer.php');
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 07.06.12
 * Time: 14:19
 * To change this template use File | Settings | File Templates.
 */
class SessionCartContainer implements CartContainer
{

  private $sessionName = 'userCart';

  public function __construct(){

    /**
     * Если человек впервые - заводим ему пустую корзину
     */
    if(!array_key_exists($this->sessionName, $_SESSION)){
      $_SESSION[$this->sessionName] = array('productList' => array(), 'serviceList' => array(), 'warrantyList' => array());
      return;
    }

    if (!array_key_exists('productList', $_SESSION[$this->sessionName])) {
      $_SESSION[$this->sessionName]['productList'] = array();
    }

    if (!array_key_exists('serviceList', $_SESSION[$this->sessionName])) {
      $_SESSION[$this->sessionName]['serviceList'] = array();
    }

    if(!array_key_exists('warrantyList', $_SESSION[$this->sessionName])){
      $_SESSION[$this->sessionName]['warrantyList'] = array();
    }

    /**
     * Очищаем корзину от продуктов и услуг с количеством меньше 1
     */
    foreach ($_SESSION[$this->sessionName]['productList'] as $productId => $quantity) {
      if ($quantity < 1) {
        unset($_SESSION[$this->sessionName]['productList'][$productId]);
      }
    }

    foreach ($_SESSION[$this->sessionName]['serviceList'] as $serviceId => $product) {
      foreach ($product as $productId => $quantity) {
        if ($quantity < 1) {
          unset($_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId]);
        }
      }
      if (count($_SESSION[$this->sessionName]['serviceList'][$serviceId]) < 1) {
        unset($_SESSION[$this->sessionName]['serviceList'][$serviceId]);
      }
    }
  }

    public function shiftProduct()
    {
        reset($_SESSION[$this->sessionName]['productList']);

        if ($_SESSION[$this->sessionName]['productList']) {
            $key = key($_SESSION[$this->sessionName]['productList']);
            unset($_SESSION[$this->sessionName]['productList'][$key]);
        }
    }

    public function hasProduct($productId)
    {
        return array_key_exists($productId, $_SESSION[$this->sessionName]['productList']);
    }

    public function hasWarranty($productId, $warrantyId) {
        return isset($_SESSION[$this->sessionName]['warrantyList'][$warrantyId][$productId]);
    }

    public function addProduct($productId)
    {
        if (!array_key_exists($productId, $_SESSION[$this->sessionName]['productList'])) {
            $_SESSION[$this->sessionName]['productList'][$productId] = 1;
        } else {
            $_SESSION[$this->sessionName]['productList'][$productId] += 1;
        }
    }

  public function setProductQuantity($productId, $quantity)
  {
    $_SESSION[$this->sessionName]['productList'][$productId] = (int)$quantity;
  }

    public function setWarranty($warrantyId, $productId, $quantity = 1) {
        if (null !== $productId) {
            $productId = (int)$productId;
        }

        // удалить ранее установленную гарантию для товара
        foreach ($_SESSION[$this->sessionName]['warrantyList'] as $i => $warrantiesByProduct) {
            if (array_key_exists($productId, $warrantiesByProduct)) {
                unset($_SESSION[$this->sessionName]['warrantyList'][$i][$productId]);
            }
        }

        if (!array_key_exists($warrantyId, $_SESSION[$this->sessionName]['warrantyList'])) {
            $_SESSION[$this->sessionName]['warrantyList'][$warrantyId] = array();
        }

        $_SESSION[$this->sessionName]['warrantyList'][$warrantyId][$productId] = (int)$quantity;
    }

  public function addService($serviceId, $quantity, $productId=null){
    if(is_null($productId)){
      $productId = 0;
    }
    else{
      $productId = (int) $productId;
    }

    if(!array_key_exists($serviceId, $_SESSION[$this->sessionName]['serviceList'])){
      $_SESSION[$this->sessionName]['serviceList'][$serviceId] = array($productId => (int) $quantity);
    }
    else{
      if(!array_key_exists($productId, $_SESSION[$this->sessionName]['serviceList'][$serviceId])){
        $_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId] = (int) $quantity;
      }
      else{
        $_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId] += (int) $quantity;
      }
    }
  }

    public function setServiceQuantity($serviceId, $quantity, $productId = null)
    {
        if (is_null($productId)) {
            $productId = 0;
        } else {
            $productId = (int)$productId;
        }

        $_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId] = (int)$quantity;
    }

    public function removeProduct($productId, $quantity = null)
    {

        $logger = \Logger::getLogger('Cart');
        \LoggerNDC::push('removeProduct');
        if (!array_key_exists($productId, $_SESSION[$this->sessionName]['productList'])) {
            $logger->error('Product with id "' . $productId . '" not found');
            return;
        }
        \LoggerNDC::pop();

        if (is_null($quantity)) {
            //удаляем целиком
            unset ($_SESSION[$this->sessionName]['productList'][$productId]);
        } else {
            $_SESSION[$this->sessionName]['productList'][$productId] -= (int)$quantity;
            if ($_SESSION[$this->sessionName]['productList'][$productId] < 1) {
                unset ($_SESSION[$this->sessionName]['productList'][$productId]);
            }
        }
    }

      public function removeService($serviceId, $quantity = null, $productId = 0)
      {

          $logger = \Logger::getLogger('Cart');
          \LoggerNDC::push('removeService');
          if (!array_key_exists($serviceId, $_SESSION[$this->sessionName]['serviceList'])) {
              $logger->error('Service with id "' . $serviceId . '" not found');
              return;
          }

          /*    $productId = (int) $productId;
      
          if(!array_key_exists($productId, $_SESSION[$this->sessionName]['serviceList'][$serviceId])){
            $logger->error('Product with id "' . $productId . '" not found');
            return;
          }*/

          if (is_null($quantity)) {
              //удаляем целиком
              unset ($_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId]);
          } else {
              $_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId] -= (int)$quantity;
              if ($_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId] < 1) {
                  unset ($_SESSION[$this->sessionName]['serviceList'][$serviceId][$productId]);
              }
          }
          \LoggerNDC::pop();
      }

      public function removeWarranty($warrantyId, $productId) {
          foreach ($_SESSION[$this->sessionName]['warrantyList'] as $i => $warrantiesByProduct) {
              if (($i == $warrantyId) && array_key_exists($productId, $warrantiesByProduct)) {
                  unset($_SESSION[$this->sessionName]['warrantyList'][$i][$productId]);
              }
          }
      }

  public function clear(){
    $_SESSION[$this->sessionName] = array('productList' => array(), 'serviceList' => array());
  }

  public function getProductsQuantity(){
    return count($_SESSION[$this->sessionName]['productList']);
  }

  public function getServicesQuantity($productId=null){
    if(is_null($productId)){
      return count($_SESSION[$this->sessionName]['serviceList']);
    }
    else{
      $productId = (int) $productId;
      $cnt = 0;
      foreach($_SESSION[$this->sessionName]['serviceList'] as $service){
        if(array_key_exists($productId, $service)){
          $cnt++;
        }
      }
      return $cnt;
    }
  }

  /**
   * @return int
   */
  public function getTotalQuantity()
  {
    $total = 0;
    foreach ($_SESSION[$this->sessionName]['serviceList'] as $service) {
      foreach ($service as $quantity) {
        $total += $quantity;
      }
    }
    foreach ($_SESSION[$this->sessionName]['productList'] as $quantity) {
      $total += $quantity;
    }
    return $total;
  }

  /**
   * @param int $serviceId
   * @param int | null $productId
   * @return int
   */
  public function getServiceQuantity($serviceId, $productId = null)
  {
    if (!array_key_exists($serviceId, $_SESSION[$this->sessionName]['serviceList'])) {
      return 0;
    }
    $quantity = 0;
    foreach ($_SESSION[$this->sessionName]['serviceList'][$serviceId] as $prodId => $serviceQuantity) {
      if ((int)$serviceQuantity < 1) {
        unset($_SESSION[$this->sessionName]['serviceList'][$serviceId][$prodId]);
      } else {
        if (is_null($productId) || $productId == $prodId) {
          $quantity += $serviceQuantity;
        }
      }
    }
    return $quantity;
  }

  public function getProductIdList()
  {
    return array_keys($_SESSION[$this->sessionName]['productList']);
  }

  /**
   * @param $productId
   * @return int
   */
  public function getProductQuantity($productId)
  {
    $productId = (int)$productId;
    if (array_key_exists($productId, $_SESSION[$this->sessionName]['productList'])) {
      if ((int)$_SESSION[$this->sessionName]['productList'][$productId] < 1) {
        unset($_SESSION[$this->sessionName]['productList'][$productId]);
        return 0;
      }
      return $_SESSION[$this->sessionName]['productList'][$productId];
    }
    return 0;
  }

  public function getServiceIdList($productId = null)
  {
    if (is_null($productId)) {
      return array_keys($_SESSION[$this->sessionName]['serviceList']);
    }

    $idList = array();

    foreach ($_SESSION[$this->sessionName]['serviceList'] as $serviceId => $service) {
      foreach ($service as $productId => $quantity) {
        $idList[] = $serviceId;
      }
    }

    return array_unique($idList);
  }

  public function getProductsQuantities()
  {
    $return = array();
    foreach ($_SESSION[$this->sessionName]['productList'] as $productId => $productQuantity) {
      $return[] = array(
        'id'       => $productId,
        'quantity' => $productQuantity
      );
    }

    return $return;
  }

  public function getServicesQuantities()
  {
    $return = array();
    foreach ($_SESSION[$this->sessionName]['serviceList'] as $serviceId => $serviceList) {
      foreach ($serviceList as $productId => $serviceQuantity) {
        $data = array(
          'id'       => $serviceId,
          'quantity' => $serviceQuantity
        );
        if (intval($productId) > 0) {
          $data['product_id'] = (int)$productId;
        }
        $return[] = $data;
      }
    }
    return $return;
  }

    public function getWarrantiesQuantities(){
        $return = array();
        foreach($_SESSION[$this->sessionName]['warrantyList'] as $warrantyId => $warrantiesByProduct) {
            foreach($warrantiesByProduct as $productId => $warrantyQuantity){
                $data =array(
                    'id'         => $warrantyId,
                    'quantity'   => $warrantyQuantity,
                    'product_id' => (int)$productId,
                );
                $return[] = $data;
            }
        }

        return $return;
    }
}
