<?php

namespace View\User;

class EditForm {
    /** @var string */
    private $firstName;
    /** @var string */
    private $middleName;
    /** @var string */
    private $lastName;
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
    /** @var string */
    private $skype;
    /** @var array */
    private $errors = array(
        'global'       => null,
        'first_name'   => null,
        'middle_name'  => null,
        'last_name'    => null,
        'sex'          => null,
        'birthday'     => null,
        'occupation'   => null,
        'email'        => null,
        'mobile_phone' => null,
        'home_phone'   => null,
        'skype'        => null,
    );

    public function __construct(array $data = array()) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('sex', $data)) $this->setSex($data['sex']);
        if (array_key_exists('birthday', $data)) {
            if (empty($data['birthday'])) {
                $this->setBirthday(null);
            }
            else try {
                if (is_array($data['birthday'])) {
                    $data['birthday'] = sprintf('%04d-%02d-%02d 00:00:00', $data['birthday']['year'], $data['birthday']['month'], $data['birthday']['day']);
                }
                $this->setBirthday(new \DateTime($data['birthday']));
            } catch (\Exception $e) {
                \App::logger()->warn($e);
            }
        }
        if (array_key_exists('occupation', $data)) $this->setOccupation($data['occupation']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('mobile_phone', $data)) $this->setMobilePhone($data['mobile_phone']);
        if (array_key_exists('home_phone', $data)) $this->setHomePhone($data['home_phone']);
        if (array_key_exists('skype', $data)) $this->setSkype($data['skype']);
    }

    public function fromEntity(\Model\User\Entity $entity) {
        $this->setFirstName($entity->getFirstName());
        $this->setMiddleName($entity->getMiddleName());
        $this->setLastName($entity->getLastName());
        $this->setSex($entity->getSex());
        $this->setBirthday($entity->getBirthday());
        $this->setOccupation($entity->getOccupation());
        $this->setEmail($entity->getEmail());
        $this->setMobilePhone($entity->getMobilePhone());
        $this->setHomePhone($entity->getHomePhone());
        $this->setSkype($entity->getSkype());
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = trim((string)$firstName);
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param \DateTime|null $birthday
     */
    public function setBirthday(\DateTime $birthday = null) {
        $this->birthday = $birthday;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday() {
        return $this->birthday;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = trim((string)$email);
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $homePhone
     */
    public function setHomePhone($homePhone) {
        $this->homePhone = trim((string)$homePhone);
    }

    /**
     * @return string
     */
    public function getHomePhone() {
        return $this->homePhone;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = trim((string)$lastName);
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName) {
        $this->middleName = trim((string)$middleName);
    }

    /**
     * @return string
     */
    public function getMiddleName() {
        return $this->middleName;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone) {
        $this->mobilePhone = trim((string)$mobilePhone);
    }

    /**
     * @return string
     */
    public function getMobilePhone() {
        return $this->mobilePhone;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation($occupation) {
        $this->occupation = trim((string)$occupation);
    }

    /**
     * @return string
     */
    public function getOccupation() {
        return $this->occupation;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex) {
        $this->sex = (int)$sex;
    }

    /**
     * @return string
     */
    public function getSex() {
        return $this->sex;
    }

    public function getSexName() {
        $names = array(
            0 => '',
            1 => 'мужской',
            2 => 'женский',
        );

        return in_array($this->sex, $names) ? $names[$this->sex] : reset($names);
    }

    /**
     * @param string $skype
     */
    public function setSkype($skype) {
        $this->skype = trim((string)$skype);
    }

    /**
     * @return string
     */
    public function getSkype() {
        return $this->skype;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}