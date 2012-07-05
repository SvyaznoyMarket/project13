<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 20.06.12
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */
class ProductKitData{
  /** @var int */
  private $quantity;
  /** @var int */
  private $productId;
  /** @var int Id продукта - кита */
  private $relatedProductId;

  public function __construct(array $data=array())
  {
    if(!empty($data['id']))    $this->productId = (int)$data['id'];
    if(!empty($data['count'])) $this->quantity  = (int)$data['count'];
  }

  /**
   * @param int $product
   */
  public function setRelatedProductId($productId)
  {
    $this->relatedProductId = $productId;
  }

  /**
   * @return int
   */
  public function getRelatedProductId()
  {
    return $this->relatedProductId;
  }

  /**
   * @param int $productId
   */
  public function setProductId($productId)
  {
    $this->productId = (int)$productId;
  }

  /**
   * @return int
   */
  public function getProductId()
  {
    return $this->productId;
  }

  /**
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = (int)$quantity;
  }

  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
}
