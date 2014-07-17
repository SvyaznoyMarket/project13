<?php

namespace View\Enterprize;

class Form extends \Form\FormAbstract {
    /** @var string */
    private $name;
    /** @var string */
    private $email;
    /** @var string */
    private $mobile;
    /** @var string */
    private $guid;
    /** @var bool */
    private $agree;
    /** @var bool */
    private $isSubscribe;
    /** @var array */
    protected $errors = array(
        'global'    => null,
        'name'      => null,
        'email'     => null,
        'mobile'    => null,
        'guid'      => null,
        'agree'     => null,
        'subscribe' => null,
    );
    /** @inhetidoc */
    protected $route = 'enterprize.form.update';

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('mobile', $data)) $this->setMobile($data['mobile']);
        if (array_key_exists('agree', $data)) $this->setAgree($data['agree']);
        if (array_key_exists('subscribe', $data)) $this->setIsSubscribe($data['subscribe']);
        if (array_key_exists('guid', $data) || array_key_exists('enterprizeToken', $data)) {
            $guid = !empty($data['enterprizeToken']) ? $data['enterprizeToken'] : $data['guid'];
            $this->setEnterprizeCoupon($guid);
        }
    }

    /**
     * @param \Model\User\Entity $entity
     */
    public function fromEntity(\Model\User\Entity $entity) {
        $this->setName($entity->getFirstName());
        $this->setEmail($entity->getEmail());
        $this->setMobile($entity->getMobilePhone());
        $this->setIsSubscribe($entity->getIsSubscribed());
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
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = (string)$email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile) {
        $this->mobile = (string)$mobile;
    }

    /**
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * @param string $guid
     */
    public function setEnterprizeCoupon($guid) {
        $this->guid = trim((string)$guid);
    }

    /**
     * @return string
     */
    public function getEnterprizeCoupon() {
        return $this->guid;
    }

    /**
     * @param boolean $agree
     */
    public function setAgree($agree) {
        $this->agree = (bool)$agree;
    }

    /**
     * @return boolean
     */
    public function getAgree() {
        return $this->agree;
    }

    /**
     * @param boolean $isSubscribe
     */
    public function setIsSubscribe($isSubscribe) {
        $this->isSubscribe = (bool)$isSubscribe;
    }

    /**
     * @return boolean
     */
    public function getIsSubscribe() {
        return $this->isSubscribe;
    }
}