<?php

namespace EnterLab\Query;

class CurlHandler implements HandlerInterface
{
    public function __construct()
    {

    }

    /**
     * @param Query $query
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function prepare(Query $query)
    {
        if (!$query instanceof CurlQueryInterface) {
            throw new \InvalidArgumentException('Неверный запрос');
        }
    }

    public function execute()
    {

    }
}