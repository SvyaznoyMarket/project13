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
    return $this->media_image;
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
}
