<?php

class myComponents extends sfComponents
{
  public function getLayout()
  {
    return $this->getController()->getActionStack()->getLastEntry()->getActionInstance()->getLayout();
  }
}