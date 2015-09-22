<?php

namespace Model\Shop;

use \Model\Point\GeoPointInterface;

class Entity implements GeoPointInterface {
    /* @var int */
    private $id;
    /* @var string */
    private $ui;
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
    /* @var bool */
    private $isReconstructed;
    /* @var Photo\Entity[] */
    private $photo = [];
    /* @var \Model\Region\Entity|null */
    private $region;
    /* @var string */
    private $subwayName;
    /* @var array */
    private $subway = [];
    /* @var array */
    private $workingTime = [];
    /** @var int|null */
    private $productCount;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('uid', $data)) $this->setUi($data['uid']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);  // FIXME: deprecated
        if (array_key_exists('slug', $data)) $this->setToken($data['slug']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('working_time', $data)) $this->setRegime($data['working_time']); // FIXME: deprecated
        if (isset($data['working_time']['common'])) $this->setRegime($data['working_time']['common']);
        if (array_key_exists('address', $data)) $this->setAddress($data['address']);

        if (array_key_exists('coord_lat', $data)) $this->setLatitude($data['coord_lat']); // FIXME: deprecated
        if (array_key_exists('coord_long', $data)) $this->setLongitude($data['coord_long']); // FIXME: deprecated
        if (isset($data['location']['longitude'])) $this->setLongitude($data['location']['longitude']);
        if (isset($data['location']['latitude'])) $this->setLatitude($data['location']['latitude']);

        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
        if (array_key_exists('way_walk', $data)) $this->setWayWalk($data['way_walk']);
        if (array_key_exists('way_auto', $data)) $this->setWayAuto($data['way_auto']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('is_reconstruction', $data)) $this->setIsReconstructed($data['is_reconstruction']);
        //if (array_key_exists('working_time_by_day', $data)) $this->setWorkingTime($data['working_time_by_day']);
        if (array_key_exists('working_time', $data)) $this->setWorkingTime($data['working_time']);
        if (array_key_exists('images', $data) && is_array($data['images'])) {
            foreach ($data['images'] as $photoData) {
                //$this->addPhoto(new Photo\Entity($photoData)); // FIXME deprecated
            }
        }
        if (array_key_exists('geo', $data)) $this->setRegion(new \Model\Region\Entity($data['geo']));
        if (array_key_exists('subway', $data)) {
            if (isset($data['subway'][0])) {
                foreach ($data['subway'] as $subwayData) {
                    $this->setSubway(new Subway\Entity($subwayData));
                }
            } else if (isset($data['subway']['name'])) {
                $this->setSubway(new Subway\Entity($data['subway']));
            }
        }
        if (isset($data['medias'][0])) {
            foreach ($data['medias'] as $mediaItem) {
                if (!isset($mediaItem['sources'][0])) continue;

                if ('image' == $mediaItem['provider']) {
                    $this->addPhoto(new \Model\Shop\Photo\Entity($mediaItem));
                }
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
        $this->regime =
            isset($regime['common'])
            ? $regime['common']
            : (
                is_scalar($regime)
                ? (string)$regime
                : null
            )
        ;
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
        return html_entity_decode($this->description);
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
     * @param \Model\Region\Entity $region
     */
    public function setRegion(\Model\Region\Entity $region)
    {
        $this->region = $region;
    }

    /**
     * @return \Model\Region\Entity
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getSubwayName()
    {
        return $this->subwayName;
    }

    /**
     * @param $swName
     */
    public function setSubwayName($swName)
    {
        $this->subwayName = (string)$swName;
    }

    /**
     * @param \Model\Shop\Subway\Entity $subway
     */
    public function setSubway($subway)
    {
        $this->subway[] = $subway;
    }

    /**
     * @return \Model\Shop\Subway\Entity[]
     */
    public function getSubway()
    {
        return $this->subway;
    }

    /**
     * @param array $workingTime
     */
    private  function setWorkingTime($workingTime)
    {
        $this->workingTime = $workingTime;
    }

    /**
     * @return array
     */
    public function getWorkingTime()
    {
        return $this->workingTime;
    }

    /**
     * @return null|array
     */
    public function getWorkingTimeToday() {
        if ((bool)$workingTime = $this->getWorkingTime()) {
            $day = lcfirst(date('l'));

            if ($workingTime[$day][0] && $workingTime[$day][1]) {
                return array_combine(['start_time', 'end_time'], $workingTime[$day]);
            }
        }

        return null;
    }

    /**
     * @param int|null $productCount
     */
    public function setProductCount($productCount) {
        $this->productCount = (null === $productCount) ? $productCount : (int)$productCount;
    }

    /**
     * @return int|null
     */
    public function getProductCount() {
        return $this->productCount;
    }

    /**
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = (string)$ui;
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
    }
}
