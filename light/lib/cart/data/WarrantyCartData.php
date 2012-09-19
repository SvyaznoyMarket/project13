<?php
namespace light;

class WarrantyCartData
{

  /**
   * @var int
   */
  private $warrantyId;

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
   * @var array
   */
  private $error = array();

  /**
   * @param array $data
   */
  public function __construct($data = array()){
    if (array_key_exists('id', $data))         $this->warrantyId   = (int)$data['id'];
    $this->relatedProductId   = (array_key_exists('product_id', $data)) ? (int)$data['product_id'] : Null;
    if (array_key_exists('quantity', $data))   $this->quantity    = (int)$data['quantity'];
    if (array_key_exists('price', $data))      $this->price       = (float)$data['price'];

    if (array_key_exists('error', $data)){
      $this->quantity    = 0;

      $this->error['message'] = (array_key_exists('message', $data['error']))? $data['error']['message'] : 'unknown error';
      $this->error['code']    = (array_key_exists('code', $data['error']))? $data['error']['code'] : 0;
    }
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
  public function getWarrantyId(){
    return $this->warrantyId;
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
