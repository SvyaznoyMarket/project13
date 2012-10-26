<?php

/**
 * Регион
 */
class RegionEntity
{
  /* @var integer */
  private $id;

  /**
   * @var string
   */
  private $token;

  /* @var boolean */
  private $isActive;

  /* @var integer */
  private $level;

  /* @var integer */
  private $lft;

  /* @var integer */
  private $rgt;

  /**
   * @var int
   */
  private $parentId;

  /**
   * @var int
   */
  private $priceListId;

  private $name;

  /** @var string */
  private $longitude;

  /** @var string */
  private $latitude;

  /** @var bool */
  private $hasTransportCompany;

  public function __construct(array $data = array()){
    if(array_key_exists('id', $data))            $this->id          = (int)$data['id'];
    if(array_key_exists('token', $data))         $this->token       = (string)$data['token'];
    if(array_key_exists('is_active', $data))     $this->isActive    = (bool)$data['is_active'];
    if(array_key_exists('name', $data))          $this->name        = (string)$data['name'];
    if(array_key_exists('level', $data))         $this->level       = (int)$data['level'];
    if(array_key_exists('lft', $data))           $this->lft         = (int)$data['lft'];
    if(array_key_exists('rgt', $data))           $this->rgt         = (int)$data['rgt'];
    if(array_key_exists('price_list_id', $data)) $this->priceListId = (int)$data['price_list_id'];
    if(array_key_exists('parent_id', $data))     $this->parentId    = (int)$data['parent_id'];

    $coords = array(
      99      => array('52.618600', '39.568900'),
      1964    => array('54.913510', '37.416799'),
      1965    => array('55.937785', '37.520213'),
      6125    => array('55.853600', '38.441100'),
      8440    => array('56.300000', '38.133300'),
      9748    => array('55.142113', '37.462712'),
      10358   => array('55.925460', '37.993401'),
      10374   => array('54.605682', '39.733343'),
      13241   => array('50.621370', '36.583042'),
      13242   => array('52.971800', '36.066341'),
      18073   => array('56.866667', '35.916667'),
      18074   => array('51.640181', '39.178619'),
      14974   => array('55.755798', '37.617636'),
      74358   => array('54.193803', '37.619028'),
      74562   => array('51.717190', '36.181714'),
      83209   => array('52.729422', '41.428073'),
      83210   => array('53.281039', '34.376235'),
    );

    if(array_key_exists($this->id, $coords)){
      $this->latitude  = $coords[$this->id][0];
      $this->longitude = $coords[$this->id][1];
    }

    if (array_key_exists('tk_available', $data)) $this->setHasTransportCompany($data['tk_available']);
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
   * @param boolean $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }

  /**
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->isActive;
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

  /**
   * @param int $lft
   */
  public function setLft($lft)
  {
    $this->lft = $lft;
  }

  /**
   * @return int
   */
  public function getLft()
  {
    return $this->lft;
  }

  /**
   * @param int $rgt
   */
  public function setRgt($rgt)
  {
    $this->rgt = $rgt;
  }

  /**
   * @return int
   */
  public function getRgt()
  {
    return $this->rgt;
  }

  /**
   * @param null|ProductCategoryEntity $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  /**
   * @return null|ProductCategoryEntity
   */
  public function getParent()
  {
    return $this->parent;
  }

  public function setChild($child)
  {
    $this->child = $child;
  }

  public function getChild()
  {
    return $this->child;
  }

  /**
   * @param PriceTypeEntity|null $priceType
   */
  public function setPriceType($priceType)
  {
    $this->priceType = $priceType;
  }

  /**
   * @return PriceTypeEntity|null
   */
  public function getPriceType()
  {
    return $this->priceType;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  /**
   * @param int $priceListId
   */
  public function setPriceListId($priceListId)
  {
    $this->priceListId = $priceListId;
  }

  /**
   * @return int
   */
  public function getPriceListId()
  {
    return $this->priceListId;
  }

  /**
   * @param int $parentId
   */
  public function setParentId($parentId)
  {
    $this->parentId = $parentId;
  }

  /**
   * @return int
   */
  public function getParentId()
  {
    return $this->parentId;
  }

  /**
   * @return string
   */
  public function getType(){
    switch($this->getLevel()){
      case 3:
        return 'city';
      case 0:
        return 'country';
    }
    return 'area';
  }

  /**
   * @param string $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * @return string
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * @param string $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * @return string
   */
  public function getLongitude()
  {
    return $this->longitude;
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
   * @param bool $hasTransportCompany
   */
  public function setHasTransportCompany($hasTransportCompany) {
    $this->hasTransportCompany = (bool)$hasTransportCompany;
  }

  /**
   * @return bool
   */
  public function getHasTransportCompany() {
    return $this->hasTransportCompany;
  }
}
