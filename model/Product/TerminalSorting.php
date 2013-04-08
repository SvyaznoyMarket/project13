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
        'score'   => [
            'name'      => 'По рейтингу',
            'direction' => [
                'desc' => ['name' => 'По возрастанию'],
                'asc'  => ['name' => 'По убыванию'],
            ],
        ],
    ];
    /** @var string */
    private $activeSort;
    /** @var string */
    private $activeDirection;

    public function __construct() {
        $this->setActiveSort('score');
        $this->setActiveDirection('desc');
    }

    /**
     * @param string $activeDirection
     * @throws \Exception
     */
    public function setActiveDirection($activeDirection) {
        if (!is_null($activeDirection) && !in_array($activeDirection, ['asc', 'desc'])) {
            throw new \Exception(sprintf('Неправильное направление сортировки %s', $activeDirection));
        }

        $this->activeDirection = (string)$activeDirection;
    }

    /**
     * @return string
     */
    public function getActiveDirection() {
        return $this->activeDirection;
    }

    /**
     * @param string $activeSort
     * @throws \Exception
     */
    public function setActiveSort($activeSort) {
        if (!is_null($activeSort) && !array_key_exists($activeSort, $this->sort)) {
            throw new \Exception(sprintf('Неправильное название сортировки %s', $activeSort));
        }

        $this->activeSort = (string)$activeSort;
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
        return
            $this->getActiveSort() && $this->getActiveDirection()
            ? [$this->getActiveSort() => $this->getActiveDirection()]
            : [];
    }
}