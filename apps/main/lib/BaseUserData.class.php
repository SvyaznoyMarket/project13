<?php

class BaseUserData
{
  public function dump()
  {
    return $this->parameterHolder->getAll();
  }
}