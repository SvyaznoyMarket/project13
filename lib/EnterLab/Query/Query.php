<?php

namespace EnterLab\Query;

class Query
{
    /** @var Dependency[] */
    private $dependencies = [];
    /** @var mixed */
    private $result;
    /** @var \Exception[] */
    private $errors = [];
    /** @var int */
    private $count = 0;

    /**
     * @param Query $query
     * @param callable $handler
     */
    final public function addDependency(Query $query, $handler)
    {
        $dependency = new Dependency();
        $dependency->setQuery($query);
        $dependency->setHandler($handler);

        $this->dependencies[] = $query;
    }

    /**
     * @param mixed $response
     */
    final public function handleResponse($response)
    {
        try {
            $this->parseResponse($response);
        } catch (\Exception $error) {
            $this->errors[] = $error;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    final public function getResult()
    {
        if (0 === $this->count) {
            $this->errors[] = new \Exception('Запрос не был подготовлен');
        }

        if ($error = reset($this->errors)) {
            throw $error;
        }

        return $this->result;
    }



    /**
     * @param \Exception $error
     */
    public function addError(\Exception $error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return \Exception[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @throws \Exception
     */
    public function prepare()
    {
        //
    }

    /**
     * @param $response
     * @throws \Exception
     */
    public function parseResponse($response)
    {
        //
    }
}