<?php

class ShopEntity
{
    /* @var int */
    private $id;
    /* @var string */
    private $token;
    /* @var string */
    private $name;
    /* @var string */
    private $regime;
    /* @var string */
    private $address;
    /* @var double */
    private $latitude;
    /* @var double */
    private $longitude;
    /* @var string */
    private $image;
    /* @var string */
    private $phone;
    /* @var string */
    private $wayWalk;
    /* @var string */
    private $wayAuto;
    /* @var string */
    private $description;
    /** @var bool */
    private $isReconstructed;
    /** @var RegionEntity */
    private $region;
    //не используется в симфони
    ///** @var Photo\Entity[] */
    //private $photo = array();
    ///* @var Panorama\Entity */
    //private $panorama;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('working_time', $data)) $this->setRegime($data['working_time']);
        if (array_key_exists('address', $data)) $this->setAddress($data['address']);
        if (array_key_exists('coord_lat', $data)) $this->setLatitude($data['coord_lat']);
        if (array_key_exists('coord_long', $data)) $this->setLongitude($data['coord_long']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
        if (array_key_exists('way_walk', $data)) $this->setWayWalk($data['way_walk']);
        if (array_key_exists('way_auto', $data)) $this->setWayAuto($data['way_auto']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('is_reconstruction', $data)) $this->setIsReconstructed($data['is_reconstruction']);
        if (array_key_exists('geo', $data)) $this->setRegion(new RegionEntity($data['geo']));
        // TODO: фото не используется в симфони
    }


    /**
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }

  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }

  /**
   * @param \double $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * @return \double
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * @param \double $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * @return \double
   */
  public function getLongitude()
  {
    return $this->longitude;
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
   * @param string $regime
   */
  public function setRegime($regime)
  {
    $this->regime = $regime;
  }

  /**
   * @return string
   */
  public function getRegime()
  {
    return $this->regime;
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
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
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
     * @param boolean $isReconstructed
     */
    public function setIsReconstructed($isReconstructed)
    {
        $this->isReconstructed = $isReconstructed;
    }

    /**
     * @return boolean
     */
    public function getIsReconstructed()
    {
        return $this->isReconstructed;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $wayAuto
     */
    public function setWayAuto($wayAuto)
    {
        $this->wayAuto = $wayAuto;
    }

    /**
     * @return string
     */
    public function getWayAuto()
    {
        return $this->wayAuto;
    }

    /**
     * @param string $wayWalk
     */
    public function setWayWalk($wayWalk)
    {
        $this->wayWalk = $wayWalk;
    }

    /**
     * @return string
     */
    public function getWayWalk()
    {
        return $this->wayWalk;
    }

    /**
     * @param \RegionEntity $region
     */
    public function setRegion(RegionEntity $region)
    {
        $this->region = $region;
    }

    /**
     * @return \RegionEntity
     */
    public function getRegion()
    {
        return $this->region;
    }
}