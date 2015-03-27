<?php

namespace Model\Product;

class CartEntity extends CompactEntity {
    /** @var Kit\Entity[] */
    protected $kit = [];

    public function __construct(array $data = []) {
        parent::__construct($data);

        if (array_key_exists('kit', $data) && is_array($data['kit'])) $this->setKit(array_map(function($data) {
            return new Kit\Entity($data);
        }, $data['kit']));
    }

    /**
     * @param Kit\Entity[] $kits
     */
    public function setKit(array $kits) {
        $this->kit = [];
        foreach ($kits as $kit) {
            $this->addKit($kit);
        }
    }

    /**
     * @param Kit\Entity $kit
     */
    public function addKit(Kit\Entity $kit) {
        $this->kit[] = $kit;
    }

    /**
     * @return Kit\Entity[]
     */
    public function getKit() {
        return $this->kit;
    }
}