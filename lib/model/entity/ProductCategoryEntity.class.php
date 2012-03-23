<?php

/**
 * Категория товара
 */
class ProductCategoryEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $token;

  /* @var string */
  private $name;

  /* @var boolean */
  private $isActive;

  /* @var boolean */
  private $hasLine;

  /* @var boolean */
  private $isFurniture;
  /** @var boolean */
  private $isShownInMenu;

  /* @var integer */
  private $level;

  /* @var integer */
  private $lft;

  /* @var integer */
  private $rgt;

  /* @var integer */
  private $position;

  /* @var string */
  private $link;

  /* @var string */
  private $mediaImage;

  /* @var integer */
  private $productViewId;

  /* @var ProductCategoryEntity|null */
  private $parent = null;

  /* @var ProductCategoryEntity[] */
  private $child;
  /** @var string */
  private $seoHeader;

  public function __construct(array $data = array())
  {
    if(array_key_exists('id', $data)) $this->setId($data['id']);
    if(array_key_exists('is_active', $data)) $this->setIsActive($data['is_active']);
    if(array_key_exists('is_furniture', $data)) $this->setIsFurniture($data['is_furniture']);
    if(array_key_exists('name', $data)) $this->setName($data['name']);
    if(array_key_exists('link', $data)) $this->setLink($data['link']);
    if(array_key_exists('token', $data)) $this->setToken($data['token']);
    if(array_key_exists('media_image', $data)) $this->setMediaImage($data['media_image']);
    if(array_key_exists('has_line', $data)) $this->setHasLine($data['has_line']);
    if(array_key_exists('is_shown_in_menu', $data)) $this->setIsShownInMenu($data['is_shown_in_menu']);
    if(array_key_exists('position', $data)) $this->setPosition($data['position']);
    if(array_key_exists('level', $data)) $this->setLevel($data['level']);
    if(array_key_exists('seo_header', $data)) $this->setSeoHeader($data['seo_header']);
    if(array_key_exists('product_view_id', $data)) $this->setProductViewId($data['product_view_id']);
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setHasLine($hasLine)
  {
    $this->hasLine = $hasLine;
  }

  public function hasLine()
  {
    return $this->hasLine;
  }

  public function setMediaImage($image)
  {
    $this->mediaImage = $image;
  }

  public function getMediaImage()
  {
    return $this->mediaImage;
  }

  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }

  public function isActive()
  {
    return $this->isActive;
  }

  public function setIsFurniture($isFurniture)
  {
    $this->isFurniture = $isFurniture;
  }

  public function isFurniture()
  {
    return $this->isFurniture;
  }

  public function setLevel($level)
  {
    $this->level = $level;
  }

  public function getLevel()
  {
    return $this->level;
  }

  public function setLft($lft)
  {
    $this->lft = $lft;
  }

  public function getLft()
  {
    return $this->lft;
  }

  public function setLink($link)
  {
    $this->link = $link;
  }

  public function getLink()
  {
    return $this->link;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setParent(ProductCategoryEntity $parent)
  {
    $this->parent = $parent;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function setPosition($position)
  {
    $this->position = $position;
  }

  public function getPosition()
  {
    return $this->position;
  }

  public function setRgt($rgt)
  {
    $this->rgt = $rgt;
  }

  public function getRgt()
  {
    return $this->rgt;
  }

  public function setChild(array $child)
  {
    $this->child = $child;
  }

  public function addChild($child)
  {
    $this->child[] = $child;
  }

  public function getChild()
  {
    return $this->child;
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
   * @param int $productViewId
   */
  public function setProductViewId($productViewId)
  {
    $this->productViewId = $productViewId;
  }

  /**
   * @return int
   */
  public function getProductViewId()
  {
    return $this->productViewId;
  }

  public function setIsShownInMenu($isShownInMenu)
  {
    $this->isShownInMenu = $isShownInMenu;
  }

  public function getIsShownInMenu()
  {
    return $this->isShownInMenu;
  }

  /**
   * @param string $seoHeader
   */
  public function setSeoHeader($seoHeader)
  {
    $this->seoHeader = $seoHeader;
  }

  /**
   * @return string
   */
  public function getSeoHeader()
  {
    return $this->seoHeader;
  }
}