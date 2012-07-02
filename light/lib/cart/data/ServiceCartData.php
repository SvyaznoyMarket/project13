<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 07.06.12
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */

class ServiceCartData
{

  /**
   * @var int
   */
  private $serviceId;

  /**
   * @var int | null
   */
  private $relatedProductId = null;

  /**
   * @var float
   */
  private $price;

  /**
   * @var int
   */
  private $quantity;

  /**
   * @param array $data
   */
  public function __construct($data = array()){
    if (array_key_exists('id', $data))         $this->serviceId   = (int)$data['id'];
    $this->relatedProductId   = (array_key_exists('product_id', $data)) ? (int)$data['product_id'] : Null;
    if (array_key_exists('quantity', $data))   $this->quantity    = (int)$data['quantity'];
    if (array_key_exists('price', $data))      $this->price       = (float)$data['price'];
  }

  /**
   * @return int|null
   */
  public function getRelatedProductId(){
    return $this->relatedProductId;
  }

  /**
   * @return int
   */
  public function getServiceId(){
    return $this->serviceId;
  }

  /**
   * @return int
   */
  public function getQuantity(){
    return $this->quantity;
  }

  /**
   * @return float
   */
  public function getPrice(){
    return $this->price;
  }

  /**
   * @return float
   */
  public function getTotalPrice(){
    return $this->quantity * $this->price;
  }

  /**
   * @param int $quantity
   */
  public function setQuantity($quantity){
    $quantity = (int) $quantity;
    $this->quantity = $quantity;
  }
}
