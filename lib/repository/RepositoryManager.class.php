<?php

class RepositoryManager
{
  public static function get($name)
  {
    $class = $name.'Repository';

    return new $class();
  }
}