<?php

namespace model\Session;


use \Model\Product\Entity as Product;

/** Объект для сохранения в сессии
 * Class FavouriteProduct
 * @package model\Session
 */
class FavouriteProduct
{

    /** @var int */
    public $id;
    /** @var string */
    public $ui;

    /**
     * @param $data []|Product
     */
    public function __construct($data) {
        if ($data instanceof Product) {
            $this->id = $data->getId();
            $this->ui = $data->getUi();
        } else if (is_array($data)) {
            if (isset($data['id'])) $this->id = $data['id'];
            if (isset($data['ui'])) $this->ui = $data['ui'];
        }
    }

}