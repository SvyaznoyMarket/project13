<?php

namespace View\Enterprize;

class Form extends FormRegistration {
    /** @var string */
    protected $guid;
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
    /** @inheritdoc */
    protected $route = 'enterprize.form.update';

    /** @inheritdoc */
    public function fromArray(array $data) {
        parent::fromArray($data);
        if (array_key_exists('guid', $data) || array_key_exists('enterprizeToken', $data)) {
            $guid = !empty($data['enterprizeToken']) ? $data['enterprizeToken'] : $data['guid'];
            $this->setEnterprizeCoupon($guid);
        }
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
}