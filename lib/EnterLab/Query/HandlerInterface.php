<?php

namespace EnterLab\Query;

interface HandlerInterface
{
    /**
     * @param Query $query
     * @return mixed
     */
    public function prepare(Query $query);

    /**
     * @return mixed
     */
    public function execute();
}