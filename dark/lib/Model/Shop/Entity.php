<?php

namespace Model\Shop;

class Entity {
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
    /* @var float */
    private $latitude;
    /* @var float */
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
    /** @var Photo\Entity[] */
    private $photo = array();
    /* @var Panorama\Entity */
    private $panorama;

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
        if (array_key_exists('images', $data) && is_array($data['images'])) {
            foreach ($data['images'] as $photoData) {
                $this->addPhoto(new Photo\Entity($photoData));
            }
        }
    }

    /**
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = (string)$address;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude) {
        $this->latitude = (float)$latitude;
    }

    /**
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude) {
        $this->longitude = (float)$longitude;
    }

    /**
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $regime
     */
    public function setRegime($regime) {
        $this->regime = (string)$regime;
    }

    /**
     * @return string
     */
    public function getRegime() {
        return $this->regime;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
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
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = (string)$phone;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param string $wayAuto
     */
    public function setWayAuto($wayAuto) {
        $this->wayAuto = (string)$wayAuto;
    }

    /**
     * @return string
     */
    public function getWayAuto() {
        return $this->wayAuto;
    }

    /**
     * @param string $wayWalk
     */
    public function setWayWalk($wayWalk) {
        $this->wayWalk = (string)$wayWalk;
    }

    /**
     * @return string
     */
    public function getWayWalk() {
        return $this->wayWalk;
    }

    /**
     * @param \Model\Shop\Panorama\Entity $panorama
     */
    public function setPanorama(Panorama\Entity $panorama = null) {
        $this->panorama = $panorama;
    }

    /**
     * @return \Model\Shop\Panorama\Entity
     */
    public function getPanorama() {
        return $this->panorama;
    }

    /**
     * @param Photo\Entity[] $photos
     */
    public function setPhoto(array $photos) {
        $this->photo = array();
        foreach ($photos as $photo) {
            $this->addPhoto($photo);
        }
    }

    /**
     * @param Photo\Entity $photo
     */
    public function addPhoto(Photo\Entity $photo) {
        $this->photo[] = $photo;
    }

    /**
     * @return Photo\Entity[]
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @param bool $isReconstructed
     */
    public function setIsReconstructed($isReconstructed) {
        $this->isReconstructed = (bool)$isReconstructed;
    }

    /**
     * @return bool
     */
    public function getIsReconstructed() {
        return $this->isReconstructed;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 1) {
        if ($this->image) {
            $urls = \App::config()->shopPhoto['url'];

            return $urls[$size] . $this->image;
        } else {
            return null;
        }
    }
}
