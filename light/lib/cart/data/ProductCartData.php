<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 07.06.12
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */

class ProductCartData
{

  /**
   * @var int
   */
  private $productId;

  /**
   * @var int
   */
  private $quantity;

  /**
   * @var float
   */
  private $price;

  /**
   * @param array $data
   */
  public function __construct($data = array()){

    if (array_key_exists('id', $data))         $this->productId   = (int)$data['id'];
    if (array_key_exists('quantity', $data))   $this->quantity    = (int)$data['quantity'];
    if (array_key_exists('price', $data))      $this->price       = (float)$data['price'];
  }

  /**
   * @return int
   */
  public function getProductId(){
    return $this->productId;
  }

  /**
   * @return int
   */
  public function getQuantity(){
    return $this->quantity;
  }

  /**
   * @return int
   */
  public function getPrice(){
    return $this->price;
  }

  /**
   * @return float
   */
  public function getTotalPrice(){
    return $this->quantity * $this->getPrice();
  }

  /**
   * @param int $quantity
   */
  public function setQuantity($quantity){
    $quantity = (int) $quantity;
    $this->quantity = $quantity;
  }
}
