<?php

namespace EnterModel;

use EnterModel\Error as Entity;

class ErrorCollection implements \IteratorAggregate, \ArrayAccess, \Countable, \JsonSerializable
{
    /** @var Entity[] */
    private $collection = [];

    /**
     * @param array $collection
     */
    public function __construct($collection = [])
    {
        foreach ($collection as $i => $item) {
            $this->collection[$i] = $item;
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }

    /**
     * @param Entity $value
     * @return Entity
     */
    public function push(Entity $value = null)
    {
        if (null === $value) {
            $value = new Entity();
        }

        $this->collection[] = $value;

        return $value;
    }

    /**
     * @param Error $value
     */
    public function remove(Entity $value)
    {
        foreach ($this->collection as $i => $entity) {
            if ($value === $entity) {
                unset($this->collection[$i]);
                break;
            }
        }
    }

    /**
     * @return Entity|null
     */
    public function reset() {
        return reset($this->collection) ?: null;
    }

    /**
     * @return Entity|null
     */
    public function end() {
        return end($this->collection) ?: null;
    }

    /**
     * @param mixed $offset
     * @param Entity $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Entity) {
            throw new \InvalidArgumentException('Неверный тип аргумента');
        }

        $this->collection[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, $this->collection)) {
            unset($this->collection[$offset]);
        }
    }

    /**
     * @param mixed $offset
     * @return Entity|null
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->collection) ? $this->collection[$offset] : null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * @return array
     */
    public function exportToArray() {
        $data = [];
        foreach ($this->collection as $i => $entity) {
            $data[$i] = $entity->exportToArray();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->collection;
    }
}