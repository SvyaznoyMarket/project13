<?php
/**
 * Created by PhpStorm.
 * User: vadimkovalenko
 * Date: 18.07.14
 * Time: 20:03
 */

namespace View\Enterprize;


class FormRegistration extends \Form\FormAbstract {
    /** @var string */
    protected $name;
    /** @var string */
    protected $email;
    /** @var string */
    protected $mobile;
    /** @var bool */
    protected $agree;
    /** @var bool */
    protected $isSubscribe;
    /** @var array */
    protected $errors = array(
        'global'        => null,
        'name'          => null,
        'email'         => null,
        'mobile'        => null,
        'agree'         => null,
        'isSubscribe'   => null,
    );
    /** @inheritdoc */
    protected $route = 'enterprize.registration.execute';

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('mobile', $data)) $this->setMobile($data['mobile']);
        if (array_key_exists('agree', $data)) $this->setAgree($data['agree']);
        if (array_key_exists('isSubscribe', $data)) $this->setIsSubscribe($data['isSubscribe']);
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
        if(!$name) {
            $this->setError('name', 'Не указано имя');
            return;
        }

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
        if(!$email) {
            $this->setError('email', 'Не указан email');
            return;
        }
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
        $mobile = trim((string)$mobile);
        $mobile = preg_replace('/^\+7/', '8', $mobile);
        $this->mobile = preg_replace('/[^0-9]/', '', $mobile);
    }

    /**
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
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

    /** @inheritdoc */
    public function __toArray() {
        return [
            'name'      => $this->getName(),
            'email'     => $this->getEmail(),
            'mobile'    => $this->getMobile(),
            'agree'     => $this->getAgree(),
            'isSubscribe' => $this->getIsSubscribe(),
        ];
    }
}