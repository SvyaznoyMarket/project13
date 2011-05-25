<?php

class myDoctrineVirtualCollection implements ArrayAccess, Iterator, Countable
{
  protected $position = 0;

  protected $records = array();
  
  public function __construct()
  {
    $this->position = 0;
  }



  public function offsetExists($offset)
  {
    return isset($this->records[$offset]);
  }

  public function offsetGet($offset) {
    return $this->records[$offset];
  }

  public function offsetSet($offset, $value) {
    if (null === $offset)
    {
      $this->records[] = $value;      
    }
    else {
      $this->records[$offset] = $value;
    }
  }

  public function offsetUnset($offset)
  {
    unset($this->records[$offset]);
  }  



  public function rewind()
  {
    $this->position = 0;
  }

  public function key()
  {
    return $this->position;
  }

  public function current()
  {
    return $this->records[$this->position];
  }

  public function next()
  {
    ++$this->position;
  }

  public function valid()
  {
    return isset($this->records[$this->position]);
  }



  public function count()
  {
    return count($this->records);
  }
}