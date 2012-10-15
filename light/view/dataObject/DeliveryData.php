<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.04.12
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */
require_once('ShopData.php');

class DeliveryData
{

  const TYPE_STANDART = 1;
  const TYPE_EXPRESS = 2;
  const TYPE_SELF = 3;
  const TYPE_SALE = 5;

  /**
   * @var integer
   */
  private $mode_id = Null;

  /**
   * @var string
   */
  private $token = Null;

  /**
   * @var string
   */
  private $name = Null;

  /**
   * @var float
   */
  private $price = Null;

  /**
   * @var array
   *
   * @code:
   *
   *  array(
   *    0 => array(
   *      'name' => 'Завтра',
   *      'value' => '2012-04-23',
   *      'shops' => array(                   //Только у самовывоза
   *        'shopId' => array(
   *          'intervalId1' => array(
   *            'time_begin' => '09:00',
   *            'time_end' => '18:00',
   *          ),
   *          'intervalId2' => array(
   *            'time_begin' => '09:00',
   *            'time_end' => '18:00',
   *          )
   *        ),
   *        'shopId2' => array(
   *          'intervalId1' => array(
   *            'time_begin' => '09:00',
   *            'time_end' => '18:00',
   *          ),
   *          'intervalId2' => array(
   *            'time_begin' => '09:00',
   *            'time_end' => '18:00',
   *          )
   *        ),
   *      'intervals' => array(                   //нет у самовывоза
   *        0 => 5,
   *        1 => 4,
   *   )
   *
   */
  private $dates = array();

  /**
   * @var ShopData[]
   */
  private $shops = array();

  /**
   * @param array $dates
   */
  public function setDates($dates)
  {
    $this->dates = $dates;
  }

  /**
   * @return array
   */
  public function getDates()
  {
    return $this->dates;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
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
   * @param ShopData[] $shops
   */
  public function setShops($shops)
  {
    $this->shops = $shops;
  }

  /**
   * @param ShopData $shop
   */
  public function addShop($shop){
    $this->shops[] = $shop;
  }

  /**
   * @return ShopData[]
   */
  public function getShops()
  {
    return $this->shops;
  }

  public function toArray(){
    $shops = array();

    foreach($this->getShops() as $shop){
      $shops[] = $shop->toArray();
    }

    return array(
      'modeId'=> $this->getModeId(),
      'name'  => $this->getName(),
      'token' => $this->getToken(),
      'price' => $this->getPrice(),
      'shops' => $shops,
      'dates' => $this->getDates()
    );
  }

  /**
   * @param int $mode_id
   */
  public function setModeId($mode_id)
  {
    $this->mode_id = $mode_id;
  }

  /**
   * @return int
   */
  public function getModeId()
  {
    return $this->mode_id;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}
