<?php

namespace Oauth\Model\Facebook;

class Entity implements \Oauth\Model\EntityInterface {
    /** @var string */
    private $id;
    /** @var string */
    private $username;

    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $name;
    /** @var string */
    private $link;
    /** @var string Всегда переопределяется \Model\User\Entity::$username */
    //private $username;
    /** @var string */
    private $birthday;
    /** @var array */
    private $hometown;
    /** @var array */
    private $location;
    /** @var array */
    private $sports;
    /** @var array */
    private $education;
    /** @var string */
    private $gender;
    /** @var string */
    private $relationshipStatus;
    /** @var string */
    private $timezone;
    /** @var string */
    private $locale;
    /** @var array */
    private $languages;
    /** @var bool */
    private $verified;
    /** @var string */
    private $updatedTime;

    public function __construct(array $data = array()) {
        $this->import($data);
    }

    public function import(array $data) {
        if (array_key_exists('username', $data)) $this->setUsername($data['username']);

        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        //if (array_key_exists('username', $data)) $this->setUsername($data['username']); Всегда переопределяется \Model\User\Entity::$username */
        if (array_key_exists('birthday', $data)) $this->setBirthday($data['birthday']);
        if (array_key_exists('hometown', $data)) $this->setHometown($data['hometown']);
        if (array_key_exists('location', $data)) $this->setLocation($data['location']);
        if (array_key_exists('sports', $data)) $this->setSports($data['sports']);
        if (array_key_exists('education', $data)) $this->setEducation($data['education']);
        if (array_key_exists('gender', $data)) $this->setGender($data['gender']);
        if (array_key_exists('relationship_status', $data)) $this->setRelationshipStatus($data['relationship_status']);
        if (array_key_exists('timezone', $data)) $this->setTimezone($data['timezone']);
        if (array_key_exists('locale', $data)) $this->setLocale($data['locale']);
        if (array_key_exists('languages', $data)) $this->setLanguages($data['languages']);
        if (array_key_exists('verified', $data)) $this->setVerified($data['verified']);
        if (array_key_exists('updated_time', $data)) $this->setUpdatedTime($data['updated_time']);
    }

    public function export() {
        $data['id'] = $this->getId();
        $data['first_name'] = $this->getFirstName();
        $data['last_name'] = $this->getLastName();
        $data['name'] = $this->getName();
        $data['link'] = $this->getLink();
        //$data['username'] = $this->getUsername(); Всегда переопределяется \Model\User\Entity::$username */
        $data['birthday'] = $this->getBirthday();
        $data['hometown'] = $this->getHometown();
        $data['location'] = $this->getLocation();
        $data['sports'] = $this->getSports();
        $data['education'] = $this->getEducation();
        $data['gender'] = $this->getGender();
        $data['relationship_status'] = $this->getRelationshipStatus();
        $data['timezone'] = $this->getTimezone();
        $data['locale'] = $this->getLocale();
        $data['languages'] = $this->getLanguages();
        $data['verified'] = $this->getVerified();
        $data['updated_time'] = $this->getUpdatedTime();

        return $data;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday($birthday) {
        $this->birthday = (string)$birthday;
    }

    /**
     * @return string
     */
    public function getBirthday() {
        return $this->birthday;
    }

    /**
     * @param array $education
     */
    public function setEducation($education) {
        $this->education = (array)$education;
    }

    /**
     * @return array
     */
    public function getEducation() {
        return $this->education;
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
     * @param string $gender
     */
    public function setGender($gender) {
        $this->gender = (string)$gender;
    }

    /**
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * @param array $hometown
     */
    public function setHometown($hometown) {
        $this->hometown = (array)$hometown;
    }

    /**
     * @return array
     */
    public function getHometown() {
        return $this->hometown;
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
     * @param array $languages
     */
    public function setLanguages($languages) {
        $this->languages = (array)$languages;
    }

    /**
     * @return array
     */
    public function getLanguages() {
        return $this->languages;
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
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale) {
        $this->locale = (string)$locale;
    }

    /**
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * @param array $location
     */
    public function setLocation($location) {
        $this->location = (array)$location;
    }

    /**
     * @return array
     */
    public function getLocation() {
        return $this->location;
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
     * @param string $relationshipStatus
     */
    public function setRelationshipStatus($relationshipStatus) {
        $this->relationshipStatus = (string)$relationshipStatus;
    }

    /**
     * @return string
     */
    public function getRelationshipStatus() {
        return $this->relationshipStatus;
    }

    /**
     * @param array $sports
     */
    public function setSports($sports) {
        $this->sports = (array)$sports;
    }

    /**
     * @return array
     */
    public function getSports() {
        return $this->sports;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone) {
        $this->timezone = (int)$timezone;
    }

    /**
     * @return string
     */
    public function getTimezone() {
        return $this->timezone;
    }

    /**
     * @param string $updatedTime
     */
    public function setUpdatedTime($updatedTime) {
        $this->updatedTime = (string)$updatedTime;
    }

    /**
     * @return string
     */
    public function getUpdatedTime() {
        return $this->updatedTime;
    }

    /**
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = (string)$username;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param bool $verified
     */
    public function setVerified($verified) {
        $this->verified = (bool)$verified;
    }

    /**
     * @return bool
     */
    public function getVerified() {
        return $this->verified;
    }
}