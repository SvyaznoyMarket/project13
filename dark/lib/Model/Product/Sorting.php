<?php

namespace Model\Product;

class Sorting {
    /** @var array */
    private $list = array(
        'price_asc'   => array(
            'name'      => 'price',
            'title'     => 'по цене (сначала дешевые)',
            'direction' => 'asc',
        ),
        'price_desc'   => array(
            'name'      => 'price',
            'title'     => 'по цене (сначала дорогие)',
            'direction' => 'desc',
        ),
        'creator_asc' => array(
            'name'      => 'creator',
            'title'     => 'по производителю (А-Я)',
            'direction' => 'asc',
        ),
        'creator_desc' => array(
            'name'      => 'creator',
            'title'     => 'по производителю (Я-А)',
            'direction' => 'desc',
        ),
        'rating'  => array(
            'name'      => 'rating',
            'title'     => 'по рейтингу',
            'direction' => 'desc',
        ),
        'score'  => array(
            'name'      => 'score',
            'title'     => 'как для своих',
            'direction' => 'desc',
        ),
    );
    /** @var string */
    private $active = 'score';

    /**
     * @return string
     */
    public function getDirection() {
        return $this->list[$this->active]['direction'];
    }

    /**
     * @param string $name
     * @param string $direction
     */
    public function setActive($name, $direction = 'asc') {
        $id = $name . '_' . $direction;
        if(isset($this->list[$id])) {
            $this->active = $id;
        }
    }

    /**
     * @return array
     */
    public function getActive() {
        return $this->list[$this->active];
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->list;
    }

    /**
     * @return array
     */
    public function dump() {
        $active = $this->getActive();

        return array($active['name'] => $active['direction']);
    }
}