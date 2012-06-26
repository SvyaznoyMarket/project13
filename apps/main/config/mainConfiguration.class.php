<?php

class mainConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    $this->initValidatorMessages();
  }

  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    $manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
    //$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );

    sfConfig::set('doctrine_model_builder_options', array(
      'baseTableClassName' => 'myDoctrineTable',
      'baseClassName'      => 'myDoctrineRecord',
    ));
  }

  protected function initValidatorMessages()
  {
    foreach (array(
      'required' => 'Не заполнено.',
      'invalid'  => 'Неверное.',
      'max'      => 'Не должно быть больше %max%.',
      'min'      => 'Не должно быть меньше %min%.',
    ) as $name => $value)
    {
      sfValidatorBase::setDefaultMessage($name, $value);
    }
  }
}
