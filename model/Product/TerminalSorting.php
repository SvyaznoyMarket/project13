<?php

namespace Model\Product;

class TerminalSorting {
    /** @var array */
    private $sort = [
        'price'   => [
            'name'      => 'По цене',
            'direction' => [
                'desc' => ['name' => 'По возрастанию'],
                'asc'  => ['name' => 'По убыванию'],
            ],
        ],
        'creator' => [
            'name'      => 'По производителю',
            'direction' => [
                'desc' => ['name' => 'А-Я'],
                'asc'  => ['name' => 'Я-А'],
            ],
        ],
        'rating'   => [
            'name'      => 'По рейтингу',
            'direction' => [
                'desc' => ['name' => 'По возрастанию'],
                'asc'  => ['name' => 'По убыванию'],
            ],
        ],
    ];
    /** @var string */
    private $activeSort = [];

    public function __construct() {
        $this->setActiveSort('rating', 'desc');
    }

    /**
     * @param string $name
     * @param string $direction
     * @throws \Exception
     */
    public function setActiveSort($name, $direction) {
        if (!array_key_exists($name, $this->sort)) {
            throw new \Exception(sprintf('Неправильное название сортировки %s', $name));
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            throw new \Exception(sprintf('Неправильное направление сортировки %s', $direction));
        }

        $this->activeSort = [$name => $direction];
    }

    /**
     * @return string
     */
    public function getActiveSort() {
        return $this->activeSort;
    }

    /**
     * @param array $sort
     */
    public function setSort($sort) {
        $this->sort = $sort;
    }

    /**
     * @return array
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * @return array
     */
    public function dump() {
        return $this->getActiveSort();
    }

    /**
     * @return array
     */
    public function all() {
        return $this->sort;
    }
}