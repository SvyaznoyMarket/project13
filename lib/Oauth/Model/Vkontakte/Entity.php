<?php

namespace Oauth\Model\Vkontakte;

class Entity implements \Oauth\Model\EntityInterface {
    /** @var string */
    private $id;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $nickname;
    /** @var string */
    private $screenName;
    /** @var int */
    private $sex;
    /** @var string */
    private $bdate;
    /** @var string */
    private $city;
    /** @var string */
    private $country;
    /** @var int */
    private $timezone;
    /** @var string */
    private $photo;
    /** @var string */
    private $photoMedium;
    /** @var string */
    private $photoBig;

    public function __construct(array $data = array()) {
        $this->import($data);
    }

    public function import(array $data) {
        if (array_key_exists('uid', $data)) $this->setId($data['uid']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('nickname', $data)) $this->setNickname($data['nickname']);
        if (array_key_exists('screen_name', $data)) $this->setScreenName($data['screen_name']);
        if (array_key_exists('sex', $data)) $this->setSex($data['sex']);
        if (array_key_exists('bdate', $data)) $this->setBdate($data['bdate']);
        if (array_key_exists('city', $data)) $this->setCity($data['city']);
        if (array_key_exists('country', $data)) $this->setCountry($data['country']);
        if (array_key_exists('timezone', $data)) $this->setCountry($data['timezone']);
        if (array_key_exists('photo', $data)) $this->setPhoto($data['photo']);
        if (array_key_exists('photo_medium', $data)) $this->setPhotoMedium($data['photo_medium']);
        if (array_key_exists('photo_big', $data)) $this->setPhotoBig($data['photo_big']);
    }

    public function export() {
        $data['uid'] = $this->getId();
        $data['first_name'] = $this->getFirstName();
        $data['last_name'] = $this->getLastName();
        $data['nickname'] = $this->getNickname();
        $data['screen_name'] = $this->getScreenName();
        $data['sex'] = $this->getSex();
        $data['bdate'] = $this->getBdate();
        $data['city'] = $this->getCity();
        $data['country'] = $this->getCountry();
        $data['timezone'] = $this->getTimezone();
        $data['photo'] = $this->getPhoto();
        $data['photo_medium'] = $this->getPhotoMedium();
        $data['photo_big'] = $this->getPhotoBig();

        return $data;
    }

    /**
     * @param string $bdate
     */
    public function setBdate($bdate) {
        $this->bdate = (string)$bdate;
    }

    /**
     * @return string
     */
    public function getBdate() {
        return $this->bdate;
    }

    /**
     * @param string $city
     */
    public function setCity($city) {
        $this->city = (string)$city;
    }

    /**
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param string $country
     */
    public function setCountry($country) {
        $this->country = (string)$country;
    }

    /**
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = (string)$firstName;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->id = (string)$id;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = (string)$lastName;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $nickname
     */
    public function setNickname($nickname) {
        $this->nickname = (string)$nickname;
    }

    /**
     * @return string
     */
    public function getNickname() {
        return $this->nickname;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo) {
        $this->photo = (string)$photo;
    }

    /**
     * @return string
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @param string $photoBig
     */
    public function setPhotoBig($photoBig) {
        $this->photoBig = (string)$photoBig;
    }

    /**
     * @return string
     */
    public function getPhotoBig() {
        return $this->photoBig;
    }

    /**
     * @param string $photoMedium
     */
    public function setPhotoMedium($photoMedium) {
        $this->photoMedium = (string)$photoMedium;
    }

    /**
     * @return string
     */
    public function getPhotoMedium() {
        return $this->photoMedium;
    }

    /**
     * @param string $screenName
     */
    public function setScreenName($screenName) {
        $this->screenName = (string)$screenName;
    }

    /**
     * @return string
     */
    public function getScreenName() {
        return $this->screenName;
    }

    /**
     * @param int $sex
     */
    public function setSex($sex) {
        $this->sex = (int)$sex;
    }

    /**
     * @return int
     */
    public function getSex() {
        return $this->sex;
    }

    /**
     * @param int $timezone
     */
    public function setTimezone($timezone) {
        $this->timezone = (int)$timezone;
    }

    /**
     * @return int
     */
    public function getTimezone() {
        return $this->timezone;
    }
}