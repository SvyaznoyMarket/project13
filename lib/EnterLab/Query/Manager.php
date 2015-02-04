<?php

namespace EnterLab\Query;

class Manager
{
    /** @var HandlerInterface */
    private $handler;
    /** @var Query[] */
    private $queries = [];

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param Query $query
     */
    public function add(Query $query)
    {
        // подготовка запроса
        try {
            $query->prepare();
            $this->queries[] = $query;
        } catch (\Exception $error) {
            $query->addError($error);
        }
    }

    public function execute()
    {
        /*
         * TODO:
         *   найти все запросы без зависимостей
         *   выполнить эти запросы
         *
         *   найти запросы, у которых в зависимостях есть все ранее выполненые запросы
         *   вызвать обработчики (closure) этих запросов
         *   если обработчики не выбрасывали Exception, то выполнить запросы
         */

        foreach ($this->queries as $query) {
            $query->prepare();
        }

        $this->handler->execute();
    }
}