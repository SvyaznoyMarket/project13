<?php

namespace Oauth\Model\Odnoklassniki;

class Entity implements \Oauth\Model\EntityInterface {
    /** @var string */
    private $id;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $birthday;
    /** @var string */
    private $age;
    /** @var string */
    private $name;
    /** @var string */
    private $hasEmail;
    /** @var string */
    private $gender;
    /** @var string $pic1 profile small icon (50x50) */
    private $pic1;
    /** @var string $pic2 profile small picture (128x128) */
    private $pic2;

    public function __construct(array $data = []) {
        $this->import($data);
    }

    public function import(array $data) {
        if (array_key_exists('uid', $data)) $this->setId($data['uid']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('birthday', $data)) $this->setBirthday($data['birthday']);
        if (array_key_exists('age', $data)) $this->setAge($data['age']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('gender', $data)) $this->setGender($data['gender']);
        if (array_key_exists('has_email', $data)) $this->setHasEmail($data['has_email']);
        if (array_key_exists('pic_1', $data)) $this->setPic1($data['pic_1']);
        if (array_key_exists('pic_2', $data)) $this->setPic2($data['pic_2']);

    }

    public function export() {
        $data['uid'] = $this->getId();
        $data['first_name'] = $this->getFirstName();
        $data['last_name'] = $this->getLastName();
        $data['birthday'] = $this->getBirthday();
        $data['age'] = $this->getAge();
        $data['name'] = $this->getName();
        $data['gender'] = $this->getGender();
        $data['has_email'] = $this->getHasEmail();
        $data['pic_1'] = $this->getPic1();
        $data['pic_2'] = $this->getPic2();

        return $data;
    }

    /**
     * @param string $age
     */
    public function setAge($age) {
        $this->age = (int)$age;
    }

    /**
     * @return string
     */
    public function getAge() {
        return $this->age;
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
     * @param string $hasEmail
     */
    public function setHasEmail($hasEmail) {
        $this->hasEmail = (bool)$hasEmail;
    }

    /**
     * @return string
     */
    public function getHasEmail() {
        return $this->hasEmail;
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
     * @param string $pic1
     */
    public function setPic1($pic1) {
        $this->pic1 = (string)$pic1;
    }

    /**
     * @return string
     */
    public function getPic1() {
        return $this->pic1;
    }

    /**
     * @param string $pic2
     */
    public function setPic2($pic2) {
        $this->pic2 = (string)$pic2;
    }

    /**
     * @return string
     */
    public function getPic2() {
        return $this->pic2;
    }
}