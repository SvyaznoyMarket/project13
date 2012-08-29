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
  private static $information = array();

  public function __construct($data = array()){
    if(array_key_exists('id', $data)){ $this->setId((int)$data['id']); }
    array_key_exists('product_id', $data) ? $this->setProductId((int)$data['id']) : $this->setProductId(0);
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
    $this->id = (int) $id;
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
    $this->productId = (int) $productId;
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

  public function getChildren()
  {
      return self::$information[$this->id]->getChildren();
  }

  public function getParent()
  {
      return self::$information[$this->id]->getParent();
  }


  public function getIsDelivery()
  {
    return self::$information[$this->id]->getIsDelivery();
  }

  public function getIsInShop()
  {
    return self::$information[$this->id]->getIsInShop();
  }

  public function getMediaImageUrl($viewId=0)
  {
    return self::$information[$this->id]->getMediaImageUrl($viewId);
  }

  public function addService($entity)
  {
    self::$information[$this->id]->addService($entity);
  }

  public function getServiceList()
  {
    return self::$information[$this->id]->getServiceList();
  }

  public function getServiceIdList()
  {
    return self::$information[$this->id]->getServiceIdList();
  }

  public function getFirstChild()
  {
    return self::$information[$this->id]->getFirstChild();
  }

  public function getLevel()
  {
    return self::$information[$this->id]->getLevel();
  }

  public function getIconClass()
  {
    return self::$information[$this->id]->getIconClass();
  }

  public function getDescriptionByIcon()
  {
    return self::$information[$this->id]->getDescriptionByIcon();
  }

  public function getNavigation()
  {
      return self::$information[$this->id]->getNavigation();
  }

  public function getLink()
  {
      return self::$information[$this->id]->getLink();
  }

  public function setServiceIdList($serviceIdList)
  {
    return self::$information[$this->id]->setServiceIdList($serviceIdList);
  }

  public function getAlikeIdList()
  {
    return self::$information[$this->id]->getAlikeIdList();
  }

  public function setAlikeList($alikeList)
  {
    return self::$information[$this->id]->setAlikeList($alikeList);
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
  private $alikeIdList;

  private $children;

  private $parent;

  private $link;

  private $level;

  private $serviceIdList;

  private $serviceList;

  private $productId;

  private $price;

  private $isInShop;
  private $isDelivery;

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
    if(array_key_exists('alike_list', $data)){ $this->setAlikeList($data['alike_list']); }
    if(array_key_exists('children', $data)){ $this->setChildren($data['children']); }
    if(array_key_exists('parent', $data)){ $this->setParent($data['parent']); }
    if(array_key_exists('link', $data)){ $this->setLink($data['link']); }
    if(array_key_exists('level', $data)){ $this->setLevel($data['level']); }
    if(array_key_exists('is_in_shop', $data)){ $this->setIsInShop($data['is_in_shop']); }
    if(array_key_exists('is_delivery', $data)){ $this->setIsDelivery($data['is_delivery']); }
  }

  public function getIsDelivery()
  {
      return $this->isDelivery;
  }

  public function setIsDelivery($isDelivery)
  {
      $this->isDelivery = $isDelivery;
  }

  public function getIsInShop()
  {
      return $this->isInShop;
  }

  public function setIsInShop($isInShop)
  {
      $this->isInShop = $isInShop;
  }

  public function getMediaImageUrl($viewId=0)
  {
    $path = $this->getMediaImage();
    if($path){
      $urls = getServicePhotoUrlList();
      return $urls[$viewId] . $path;
    }
    else
    {
      return null;
    }
  }

  public function addService($entity)
  {
    $this->serviceList[] = $entity;
  }

  public function getServiceList()
  {
      return $this->serviceList;
  }

  public function setServiceList(array $service_list)
  {
    $this->serviceList = array();
    foreach($service_list as $service)
    {
      $this->addService($service);
    }
  }

  public function setServiceIdList($serviceIdList)
  {
      $this->serviceIdList = $serviceIdList;
  }

  public function getServiceIdList()
  {
      return $this->serviceIdList;
  }

  public function getFirstChild()
  {
    return $this->children?$this->children[0]:null;
  }

  public function getLevel()
  {
      return $this->level;
  }

  public function setLevel($level)
  {
      $this->level = $level;
  }

  public function getIconClass()
  {
      if (strpos($this->token, 'bitovaya-tehnika') !== false) return 'icon2';
      if (strpos($this->token, 'elektronika') !== false) return 'icon3';
      if (strpos($this->token, 'sport') !== false) return 'icon4';
      if (strpos($this->token, 'mebel') !== false) return 'icon1';
      return null;
  }

  public function getDescriptionByIcon()
  {
      $text = null;
      switch($this->getIconClass()){
          case 'icon1':
              $text = 'Мы соберем любой шкаф, и при этом не останется ни одной «лишней» детали. Мы занесем диван хоть на 35-й этаж (а можем и на 36-й). Мы повесим все необходимые шкафчики на кухне в правильной последовательности (и обязательно помоем за собой пол).';
              break;
          case 'icon2':
              $text = 'Мы подключим стиральную машину хоть в ванной, хоть на кухне. Мы установим кондиционер и выведем все трубки так, чтобы ничего не капало. Мы встроим варочную панель и духовку — друг над другом или в разных концах кухни.';
              break;
          case 'icon3':
              $text = 'Мы знаем, какими вирусами болеют компьютеры и как их лечить. Мы умеем тренировать ПК, чтобы они были мощными и быстрыми. Мы умеем их воспитывать, и даже самые «дикие» становятся «домашними» и ласковыми. Мы познакомим вас с технологиями Wi-Fi и WiMAX, а также научим пользоваться всеми возможностями iPhone.';
              break;
          case 'icon4':
              $text = 'Мы соберем велосипед и научим вас разбирать его в случае необходимости. Мы установим крепления на лыжи (беговые или горные) или даже на сноуборд.';
              break;
      }
      return $text;
  }

    public function getNavigation()
    {
        if($this->parent){
            $list = $this->parent->getNavigation();
        }else{
            $list = array(
                array(
                    'name' => 'F1 Сервис',
                    'url' => App::getRouter()->createUrl('service.index')
                )
            );
        }
        $list[] = array(
            'name' => $this->getName(),
            'url' => $this->getLink(),
        );
        return $list;
    }

  public function getLink()
  {
      return $this->link;
  }

  public function setLink($link)
  {
      $this->link = $link;
  }

  public function setParent($parent)
  {
      $this->parent = new ServiceData($parent);
  }

  public function getParent()
  {
      return $this->parent;
  }

  public function setChildren($childrenList)
  {
    foreach($childrenList as $children)
    {
        $this->children[] = new ServiceData($children);
    }
  }

  public function getChildren()
  {
      return $this->children;
  }

  public function setAlikeList($alike_list)
  {
    $this->alikeList = $alike_list;
  }

  public function getAlikeList()
  {
    return $this->alikeList;
  }

    public function addAlikeId($id)
    {
        $this->alikeIdList[] = (int)$id;
    }

    public function getAlikeIdList()
    {
        return $this->alikeIdList;
    }

  /**
   * @param array $category
   */
  public function setCategoryList($category)
  {
    $this->categoryList = $category;
  }

    public function addCategory($entity)
    {
        $this->categoryList[] = $entity;
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
