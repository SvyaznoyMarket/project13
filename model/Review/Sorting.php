<?php

namespace Model\Review;

class Sorting {
    const NAME_DEFAULT = 'helpful';

    /** @var array */
    private $list = [
        'default'  => [
            'name'      => self::NAME_DEFAULT,
            'title'     => 'Полезности &#9660;',
            'direction' => 'desc',
        ],
        'hits_desc'  => [
            'name'      => self::NAME_DEFAULT,
            'title'     => 'Полезности &#9650;',
            'direction' => 'asc',
        ],
        'price_desc'   => [
            'name'      => 'date',
            'title'     => 'По дате &#9660;',
            'direction' => 'desc',
        ],
        'price_asc'   => [
            'name'      => 'date',
            'title'     => 'По дате &#9650;',
            'direction' => 'asc',
        ],
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
    public function setActive($name, $direction = 'desc') {
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