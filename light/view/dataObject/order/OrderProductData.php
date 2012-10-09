<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 31.07.12
 * Time: 15:05
 * To change this template use File | Settings | File Templates.
 */
class OrderProductData
{

  /** @var int */
  private $id;

  /** @var int */
  private $quantity;

  public function __construct($data = array()){
    if(!is_array($data)){
      return;
    }
    if(array_key_exists('id', $data)){$this->id = (int) $data['id'];}
    if(array_key_exists('quantity', $data)){$this->quantity = (int) $data['quantity'];}
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }

  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
}
