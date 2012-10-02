<?php

namespace Model\User;

class Entity {
    /** @var string */
    private $token;
    /** @var /DateTime */
    private $tokenExpiredAt;

    /** @var int */
    private $id;
    /** @var string */
    private $ui;
    /** @var int */
    private $typeId;
    /** @var bool */
    private $isActive;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $middleName;
    /** @var string */
    private $sex;
    /** @var \DateTime|null */
    private $birthday;
    /** @var string */
    private $occupation;
    /** @var string */
    private $email;
    /** @var string */
    private $mobilePhone;
    /** @var string */
    private $homePhone;
    /** @var bool */
    private $isCorporative;
    /** @var string */
    private $skype;
    /** @var string */
    private $identity;
    /** @var int */
    private $cityId;
    /** @var int */
    private $regionId;
    /** @var string */
    private $address;
    /** @var string */
    private $zipCode;
    /** @var bool */
    private $isSubscribed;
    /** @var string */
    private $ipAddress;
    /** @var \DateTime|null */
    private $lastLoginAt;
    /** @var \DateTime|null */
    private $createdAt;
    /** @var \DateTime|null */
    private $updatedAt;
    /** @var \Model\Region\Entity|null */
    private $city;

    public function __construct(array $data = []) {

        $this->import($data);
    }

    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('ui', $data)) $this->setUi($data['ui']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('is_active', $data)) $this->setIsActive($data['is_active']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
        if (array_key_exists('sex', $data)) $this->setSex($data['sex']);
        if (array_key_exists('birthday', $data)) $this->setBirthday($data['birthday'] ? new \DateTime($data['birthday']) : null);
        if (array_key_exists('occupation', $data)) $this->setOccupation($data['occupation']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('phone', $data)) $this->setHomePhone($data['phone']);
        if (array_key_exists('mobile', $data)) $this->setMobilePhone($data['mobile']);
        if (array_key_exists('is_corporative', $data)) $this->setIsCorporative($data['is_corporative']);
        if (array_key_exists('skype', $data)) $this->setSkype($data['skype']);
        if (array_key_exists('identity', $data)) $this->setIdentity($data['identity']);
        if (array_key_exists('geo_id', $data)) $this->setCityId($data['geo_id']);
        if (array_key_exists('region_id', $data)) $this->setRegionId($data['region_id']);
        if (array_key_exists('address', $data)) $this->setAddress($data['address']);
        if (array_key_exists('zip_code', $data)) $this->setZipCode($data['zip_code']);
        if (array_key_exists('is_subscribe', $data)) $this->setIsSubscribed($data['is_subscribe']);
        if (array_key_exists('ip', $data)) $this->setIpAddress($data['ip']);
        if (array_key_exists('last_login', $data)) $this->setLastLoginAt($data['last_login'] ? new \DateTime($data['last_login']) : null);
        if (array_key_exists('added', $data)) $this->setCreatedAt($data['added'] ? new \DateTime($data['added']) : null);
        if (array_key_exists('updated', $data)) $this->setUpdatedAt($data['updated'] ? new \DateTime($data['updated']) : null);
        if (array_key_exists('geo', $data) && is_array($data['geo'])) $this->setCity(new \Model\Region\Entity($data['geo']));
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = (string)$address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \DateTime|null $birthday
     */
    public function setBirthday(\DateTime $birthday = null)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \Model\Region\Entity|null $city
     */
    public function setCity(\Model\Region\Entity $city = null)
    {
        $this->city = $city;
    }

    /**
     * @return \Model\Region\Entity
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId = null)
    {
        $this->cityId = $cityId ? (int)$cityId : null;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = (string)$email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = (string)$firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $homePhone
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = (string)$homePhone;
    }

    /**
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = (string)$identity;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = (string)$ipAddress;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (bool)$isActive;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isCorporative
     */
    public function setIsCorporative($isCorporative)
    {
        $this->isCorporative = (bool)$isCorporative;
    }

    /**
     * @return boolean
     */
    public function getIsCorporative()
    {
        return $this->isCorporative;
    }

    /**
     * @param boolean $isSubscribed
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = (bool)$isSubscribed;
    }

    /**
     * @return boolean
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * @param \DateTime $lastLoginAt
     */
    public function setLastLoginAt(\DateTime $lastLoginAt = null)
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = (string)$lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = (string)$middleName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = (string)$mobilePhone;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation($occupation)
    {
        $this->occupation = (string)$occupation;
    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param int $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = (int)$regionId;
    }

    /**
     * @return int
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex)
    {
        $this->sex = (int)$sex;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $skype
     */
    public function setSkype($skype)
    {
        $this->skype = (string)$skype;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param  $tokenExpiredAt|null
     */
    public function setTokenExpiredAt(\DateTime $tokenExpiredAt = null)
    {
        $this->tokenExpiredAt = $tokenExpiredAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getTokenExpiredAt()
    {
        return $this->tokenExpiredAt;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param string $ui
     */
    public function setUi($ui)
    {
        $this->ui = (string)$ui;
    }

    /**
     * @return string
     */
    public function getUi()
    {
        return $this->ui;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = (string)$zipCode;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }
}
