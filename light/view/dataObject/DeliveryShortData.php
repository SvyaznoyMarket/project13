<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 24.04.12
 * Time: 10:54
 * To change this template use File | Settings | File Templates.
 */
class DeliveryShortData
{

  /**
   * @var int
   */
  private $id = Null;

  /**
   * @var int
   */
  private $type_id = Null;

  /**
   * @var float
   */
  private $price = Null;

  /**
   * @var string
   */
  private $earliestDate = Null;

  /**
   * @param string $earliestDate
   */
  public function setEarliestDate($earliestDate)
  {
    $this->earliestDate = $earliestDate;
  }

  /**
   * @return string
   */
  public function getEarliestDate()
  {
    return $this->earliestDate;
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
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param int $type_id
   */
  public function setTypeId($type_id)
  {
    $this->type_id = $type_id;
  }

  /**
   * @return int
   */
  public function getTypeId()
  {
    return $this->type_id;
  }
}
