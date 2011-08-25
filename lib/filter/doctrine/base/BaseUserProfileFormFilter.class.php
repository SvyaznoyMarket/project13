<?php

/**
 * UserProfile filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUserProfileFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'source_id' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'      => new sfWidgetFormChoice(array('choices' => array('' => '', 'vkontakte' => 'vkontakte', 'facebook' => 'facebook', 'twitter' => 'twitter', 'gmail' => 'gmail', 'mail' => 'mail', 'live_journal' => 'live_journal', 'yandex' => 'yandex'))),
      'user_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'content'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'source_id' => new sfValidatorPass(array('required' => false)),
      'type'      => new sfValidatorChoice(array('required' => false, 'choices' => array('vkontakte' => 'vkontakte', 'facebook' => 'facebook', 'twitter' => 'twitter', 'gmail' => 'gmail', 'mail' => 'mail', 'live_journal' => 'live_journal', 'yandex' => 'yandex'))),
      'user_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'content'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('user_profile_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserProfile';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'source_id' => 'Text',
      'type'      => 'Enum',
      'user_id'   => 'ForeignKey',
      'content'   => 'Text',
    );
  }
}
