<?php

namespace EnterLab\Query;

class Dependency
{
    /** @var Query */
    private $query;
    /** @var callable */
    private $handler;

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param callable $handler
     */
    public function setHandler($handler)
    {
        if (!is_callable($handler)) {
            throw new \InvalidArgumentException('Неверный обработчик зависимости');
        }

        $this->handler = $handler;
    }
}