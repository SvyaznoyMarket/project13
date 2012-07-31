<?php

class ServiceCategoryEntity
{
  /** @var int */
  private $id;
  /** @var int */
  private $parent_id;
  /** @var int */
  private $level;
  /** @var string */
  private $link;
  /** @var string */
  private $token;
  /** @var string */
  private $name;
  /** @var string */
  private $media_image;
  /** @var ServiceCategoryEntity[] */
  private $children = array();
  /** @var ServiceCategoryEntity|null */
  private $parent;
  /** @var int[] */
  private $service_id_list=array();
  /** @var ServiceEntity[] */
  private $service_list=array();

  public function __construct(array $data = array())
  {
    if(array_key_exists('id', $data))           $this->id           = (int)$data['id'];
    if(array_key_exists('parent_id', $data))    $this->parent_id    = (int)$data['parent_id'];
    if(array_key_exists('level', $data))        $this->level        = (int)$data['level'];
    if(array_key_exists('link', $data))         $this->link         = (string)$data['link'];
    if(array_key_exists('token', $data))        $this->token        = (string)$data['token'];
    if(array_key_exists('name', $data))         $this->name         = (string)$data['name'];
    if(array_key_exists('media_image', $data))  $this->media_image  = (string)$data['media_image'];
  }

  /**
   * @param ServiceCategoryEntity[] $children
   */
  public function setChildren(array $children)
  {
    $this->children = array();
    foreach($children as $child)
      $this->addChild($child);
  }

  /**
   * @param ServiceCategoryEntity $child
   */
  public function addChild(ServiceCategoryEntity $child)
  {
    $this->children[] = $child;
  }

  /**
   * @return ServiceCategoryEntity[]
   */
  public function getChildren()
  {
    return $this->children;
  }

  public function getFirstChild()
  {
    return $this->children?$this->children[0]:null;
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
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * @param string $media_image
   */
  public function setMediaImage($media_image)
  {
    $this->media_image = $media_image;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    if($this->media_image){
      return $this->media_image;
    }
    elseif($this->service_list && $this->service_list[0]->getMediaImage()){
      return $this->service_list[0]->getMediaImage();
    }
    else{
      return null;
    }
  }

  /**
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId=0)
  {
    $path = $this->getMediaImage();
    if($path){
      $urls = sfConfig::get('app_service_photo_url');
      return $urls[$viewId] . $path;
    }
    else{
      return null;
    }
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
   * @param null|\ServiceCategoryEntity $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  /**
   * @return null|\ServiceCategoryEntity
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * @param int $parent_id
   */
  public function setParentId($parent_id)
  {
    $this->parent_id = $parent_id;
  }

  /**
   * @return int
   */
  public function getParentId()
  {
    return $this->parent_id;
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
   * @param int $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }

  /**
   * @return int
   */
  public function getLevel()
  {
    return $this->level;
  }

  public function setServiceIdList(array $service_id_list)
  {
    $this->service_id_list = $service_id_list;
  }

  public function getServiceIdList()
  {
    return $this->service_id_list;
  }

  /**
   * @param ServiceEntity[] $service_list
   */
  public function setServiceList(array $service_list)
  {
    $this->service_list = array();
    foreach($service_list as $service)
      $this->addService($service);
  }

  public function addService(ServiceEntity $entity)
  {
    $this->service_list[] = $entity;
  }

  public function getServiceList()
  {
    return $this->service_list;
  }

  /**
   * @return array
   */
  public function getNavigation()
  {
    if($this->parent){
      $list = $this->parent->getNavigation();
    }else{
      $list = array(
        array(
          'name' => 'F1 Сервис',
          'url' => url_for('service_index'),
        )
      );
    }
    $list[] = array(
      'name' => $this->getName(),
      'url' => $this->getLink(),
    );
    return $list;
  }

  public function getIconClass()
  {
    if (strpos($this->token, 'bitovaya-tehnika') !== false) return 'icon2';
    if (strpos($this->token, 'elektronika') !== false) return 'icon3';
    if (strpos($this->token, 'sport') !== false) return 'icon4';
    if (strpos($this->token, 'mebel') !== false) return 'icon1';
    return null;
  }

  public function getDescription()
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
}