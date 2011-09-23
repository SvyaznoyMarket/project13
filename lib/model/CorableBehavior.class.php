<?php

class Doctrine_Template_Corable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->hasColumn('core_id', 'integer', 20, array(
         'type' => 'integer',
         'notnull' => false,
         'comment' => 'ид записи в Core',
         'length' => 20,
         ));

      $this->addListener(new Doctrine_Template_Listener_Corable($this->_options));
  }
}
