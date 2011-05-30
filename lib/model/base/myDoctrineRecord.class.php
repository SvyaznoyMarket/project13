<?php

abstract class myDoctrineRecord extends Doctrine_Record
{
  public function toParams()
  {
    return array(
      $this->getTable()->getQueryRootAlias() => $this->id,
    );
  }
}