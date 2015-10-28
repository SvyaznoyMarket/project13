<?php

namespace Model\Review {
    class Sorting {
        /** @var Sorting\Sort[] */
        public $listByToken = [];
        /** @var Sorting\Sort|null */
        private $active;

        public function __construct() {
            foreach (
                [
                    [
                        'token'     => 'helpful',
                        'name'      => 'По полезности',
                        'direction' => 'desc',
                        'isActive'  => true,
                    ],
                    [
                        'token'     => 'date',
                        'name'      => 'По дате',
                        'direction' => 'desc',
                        'isActive'  => false,
                    ],
                ]
                as $item
            ) {
                $sort = new Sorting\Sort();
                foreach ($item as $k => $v) {
                    $sort->{$k} = $v;
                }

                $this->listByToken[$item['token']] = $sort;
                if ($sort->isActive) {
                    $this->active = $sort;
                }
            }

        }

        /**
         * @param string $token
         * @param string $direction
         */
        public function setActive($token, $direction) {
            $sort = !empty($this->listByToken[$token]) ? $this->listByToken[$token] : null;
            if (!$sort) {
                throw new \InvalidArgumentException(sprintf('Неверный токен сортировки %s', $token));
            }
            if (!in_array($direction, ['asc', 'desc'])) {
                throw new \InvalidArgumentException(sprintf('Неверное направление сортировки %s', $direction));
            }

            foreach ($this->listByToken as $iSort) {
                $iSort->isActive = false;
            }

            $sort->isActive = true;
            $sort->direction = $direction;
            $this->active = $sort;
        }

        /**
         * @return Sorting\Sort
         */
        public function getActive() {
            return $this->active;
        }
    }
}

namespace Model\Review\Sorting {
    class Sort {
        /** @var string */
        public $token;
        /** @var string */
        public $name;
        /** @var string */
        public $direction;
        /** @var bool */
        public $isActive = false;

        /**
         * Получает значение-переключатель url-параметра
         * @return string
         */
        public function getSwitchValue() {
            return implode('-', [$this->token, 'desc' === $this->direction ? 'asc' : 'desc']);
        }
    }
}