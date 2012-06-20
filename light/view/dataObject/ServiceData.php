<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.06.12
 * Time: 16:32
 * To change this template use File | Settings | File Templates.
 */
class ServiceData
{
  /**
   * @var int
   */
  private $id;

  /**
   * @var float;
   */
  private $price;

  /**
   * @var int id продукта, к которому относится сервис, 0 если не привязан ни к какому
   */
  private $productId;

  /**
   * @var ServiceInfo[] key - service ID
   */
  private static $information;

  public function __construct($data = array()){
    if(array_key_exists('id', $data)){ $this->setId((int) $data['id']); }
    array_key_exists('product_id', $data) ? $this->setId((int) $data['id']) : $this->setId(0);
    if(array_key_exists('price', $data)){ $this->setPrice( $data['price']); }
    if(!array_key_exists($this->getId(), self::$information)){
      $info = new ServiceInfo($data);
      self::$information[$this->getId()] = $info;
    }
  }

  /**
   * @return float
   */
  public function getPrice(){
    return $this->price;
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
  public function getId(){
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id){
    $this-> id = (int) $id;
  }

  /**
   * @param float $price
   */
  public function setPrice($price){
    $this->price = $price;
  }

  /**
   * @param int $productId
   */
  public function setProductId($productId){
    $productId = (int) $productId;
  }

  /**
   * @return int []
   */
  public function getAlikeList()
  {
    return self::$information[$this->id]->getAlikeList();
  }

  /**
   * @return array
   */
  public function getCategoryList()
  {
    return self::$information[$this->id]->getCategoryList();
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return self::$information[$this->id]->getDescription();
  }

  /**
   * @return boolean
   */
  public function getIsActive()
  {
    return self::$information[$this->id]->getIsActive();
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return self::$information[$this->id]->getMediaImage();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return self::$information[$this->id]->getName();
  }

  /**
   * @return boolean
   */
  public function getOnlyInShop()
  {
    return self::$information[$this->id]->getOnlyInShop();
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return self::$information[$this->id]->getToken();
  }

  /**
   * @return string
   */
  public function getWork()
  {
    return self::$information[$this->id]->getWork();
  }
}

class ServiceInfo
{

  /**
   * @var int
   */
  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $token;

  /**
   * @var string
   */
  private $description;

  /**
   * @var string
   */
  private $work;

  /**
   * @var string
   */
  private $mediaImage;

  /**
   * @var bool
   */
  private $onlyInShop;

  /**
   * @var bool
   */
  private $is_active;

  /**
   * @var array
   */
  private $categoryList;

  /**
   * @var int [] //id похожих услуг
   */
  private $alikeList;

  /**
   * @param array $data
   */
  public function __construct($data=array()){
    if(array_key_exists('id', $data)){ $this->setId((int) $data['id']); }
    if(array_key_exists('name', $data)){ $this->setName((string) $data['name']); }
    if(array_key_exists('token', $data)){ $this->setToken((string) $data['token']); }
    if(array_key_exists('description', $data)){ $this->setDescription((string) $data['description']); }
    if(array_key_exists('work', $data)){ $this->setWork((string) $data['work']); }
    if(array_key_exists('media_image', $data)){ $this->setMediaImage((string) $data['media_image']); }
    if(array_key_exists('is_active', $data)){ $this->setIsActive((bool) $data['is_active']); }
    if(array_key_exists('only_inshop', $data)){ $this->setOnlyInShop((bool) $data['only_inshop']); }
    if(array_key_exists('category_list', $data)){ $this->setCategoryList($data['category_list']); }
    if(array_key_exists('alike_list', $data)){ $this->setAlike($data['alike_list']); }
  }

  public function setAlikeList($alike_list)
  {
    $this->alikeList = $alike_list;
  }

  public function getAlikeList()
  {
    return $this->alikeList;
  }

  /**
   * @param array $category
   */
  public function setCategoryList($category)
  {
    $this->categoryList = $category;
  }

  /**
   * @return array
   */
  public function getCategoryList()
  {
    return $this->categoryList;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
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
   * @param boolean $is_active
   */
  public function setIsActive($is_active)
  {
    $this->is_active = $is_active;
  }

  /**
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->is_active;
  }

  /**
   * @param string $mediaImage
   */
  public function setMediaImage($mediaImage)
  {
    $this->mediaImage = $mediaImage;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
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
   * @param boolean $onlyInShop
   */
  public function setOnlyInShop($onlyInShop)
  {
    $this->onlyInShop = $onlyInShop;
  }

  /**
   * @return boolean
   */
  public function getOnlyInShop()
  {
    return $this->onlyInShop;
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

  /**
   * @param string $work
   */
  public function setWork($work)
  {
    $this->work = $work;
  }

  /**
   * @return string
   */
  public function getWork()
  {
    return $this->work;
  }
}
