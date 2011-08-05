<?php

class OrderStep1Form extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['region_id'] = new sfWidgetFormDoctrineChoice(array('model' => 'Region', 'add_empty' => true));

    $this->useFields(array(
      'region_id',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }
}