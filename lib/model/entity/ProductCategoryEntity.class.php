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
  private $parent;

  /** @var int */
  private $parentId;

  /* @var ProductCategoryEntity[] */
  private $children = array();
  /** @var string */
  private $seoHeader;
  /** @var int */
  private $productCount;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))                $this->id            = (int)$data['id'];
    if (array_key_exists('is_active', $data))         $this->isActive      = (bool)$data['is_active'];
    if (array_key_exists('is_furniture', $data))      $this->isFurniture   = (bool)$data['is_furniture'];
    if (array_key_exists('name', $data))              $this->name          = (string)$data['name'];
    if (array_key_exists('link', $data))              $this->link          = (string)$data['link'];
    if (array_key_exists('token', $data))             $this->token         = (string)$data['token'];
    if (array_key_exists('media_image', $data))       $this->mediaImage    = (string)$data['media_image'];
    if (array_key_exists('has_line', $data))          $this->hasLine       = (bool)$data['has_line'];
    if (array_key_exists('is_shown_in_menu', $data))  $this->isShownInMenu = (bool)$data['is_shown_in_menu'];
    if (array_key_exists('position', $data))          $this->position      = (int)$data['position'];
    if (array_key_exists('level', $data))             $this->level         = (int)$data['level'];
    if (array_key_exists('seo_header', $data))        $this->seoHeader     = (string)$data['seo_header'];
    if (array_key_exists('product_view_id', $data))   $this->productViewId = (int)$data['product_view_id'];
    if (array_key_exists('parent_id', $data))         $this->parentId      = (int)$data['parent_id'];
    if (array_key_exists('product_count', $data))     $this->productCount  = (int)$data['product_count'];
  }

  public function __toString() {
    return (string)$this->getName();
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

  public function getMediaImageUrl($size = 0)
  {
    if ($this->mediaImage) {
      $urls = sfConfig::get('app_category_photo_url');
      return $urls[$size] . $this->mediaImage;
    }
    else {
      return null;
    }
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

  public function setChildren(array $child)
  {
    $this->children = $child;
  }

  public function addChild($child)
  {
    $this->children[] = $child;
  }

  public function getChildren()
  {
    return $this->children;
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
   * @return string
   */
  public function getTokenPrefix()
  {
    $tokenPrefix = explode('/', $this->token);
    $tokenPrefix = reset($tokenPrefix);

    return $tokenPrefix;
  }

  /**
   * @param int $productViewId
   */
  public function setProductViewId($productViewId)
  {
    $this->productViewId = $productViewId;
  }

  /**
   * @return string
   */
  public function getProductView()
  {
    $return = null;

    if (1 == $this->productViewId) {
      $return = 'compact';
    }
    else if (2 == $this->productViewId) {
      $return = 'expanded';
    }

    return $return;
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

  /**
   * @param int $parentId
   */
  public function setParentId($parentId)
  {
    $this->parentId = (int)$parentId;
  }

  /**
   * @return int
   */
  public function getParentId()
  {
    return $this->parentId;
  }

  /**
   * @param int $id Core id
   * @return boolean
   */
  public function getHasChild($id)
  {
    if ($this->children) {
      foreach ($this->children as $child) {
        if ($child->getId() == $id)
          return true;
        if ($child->getHasChild($id))
          return true;
      }
    }
    return false;
  }

  /**
   * @param int $id
   * @return null|ProductCategoryEntity
   */
  public function getNode($id)
  {
    if ($this->getId() == $id)
      return $this;
    if ($this->children)
      foreach ($this->children as $child)
        if ($node = $child->getNode($id))
          return $node;
    return null;
  }

  public function getHasChildren()
  {
    return (boolean)$this->children;
  }

  /**
   * @return boolean
   */
  public function getHasLine()
  {
    return $this->hasLine;
  }

  /**
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->isActive;
  }

  /**
   * @return boolean
   */
  public function getIsFurniture()
  {
    return $this->isFurniture;
  }

  /**
   * @param int $product_count
   */
  public function setProductCount($product_count)
  {
    $this->productCount = (int)$product_count;
  }

  /**
   * @return int
   */
  public function getProductCount()
  {
    return $this->productCount;
  }

  public function isRoot() {
    return 1 == $this->level;
  }
}