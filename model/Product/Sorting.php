<?php

namespace Model\Product;

class Sorting {
    const NAME_DEFAULT = 'default';

    /** @var array */
    private $list = [
        'default'  => [
            'name'      => self::NAME_DEFAULT,
            'title'     => 'Автоматически',
            'direction' => 'desc',
        ],
        'hits_desc'  => [
            'name'      => 'hits',
            'title'     => 'Хиты продаж',
            'direction' => 'desc',
        ],
        'price_asc'   => [
            'name'      => 'price',
            'title'     => 'По цене &#9650;',
            'direction' => 'asc',
        ],
        'price_desc'   => [
            'name'      => 'price',
            'title'     => 'По цене &#9660;',
            'direction' => 'desc',
        ],
        'creator_asc' => [
            'name'      => 'creator',
            'title'     => 'Бренды А&#8594;Я',
            'direction' => 'asc',
        ],
        'creator_desc' => [
            'name'      => 'creator',
            'title'     => 'Бренды Я&#8592;А',
            'direction' => 'desc',
        ],
        /*
        'rating'  => [
             'name'      => 'rating',
             'title'     => 'по рейтингу',
             'direction' => 'desc',
        ],
        */
    ];
    /** @var string */
    private $active = 'default';

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

    /**
     * @return bool
     */
    public function isDefault() {
        $active = $this->getActive();

        return $active['name'] == self::NAME_DEFAULT;
    }
}