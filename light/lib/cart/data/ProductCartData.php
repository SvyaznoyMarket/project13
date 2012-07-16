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
   * @var array
   */
  private $error = array();

  /**
   * @param array $data
   */
  public function __construct($data = array()){

    if (array_key_exists('id', $data))         $this->productId   = (int)$data['id'];
    if (array_key_exists('quantity', $data))   $this->quantity    = (int)$data['quantity'];
    if (array_key_exists('price', $data))      $this->price       = (float)$data['price'];

    if (array_key_exists('error', $data)){
      $this->quantity    = 0;

      $this->error['message'] = (array_key_exists('message', $data['error']))? $data['error']['message'] : 'unknown error';
      $this->error['code']    = (array_key_exists('code', $data['error']))? $data['error']['code'] : 0;
    }
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

  /**
   * @return array
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * @return bool
   */
  public function hasError(){
    return (count($this->error) >1 );
  }
}
