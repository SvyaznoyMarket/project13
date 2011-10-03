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
      'source_id'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'       => new sfWidgetFormChoice(array('choices' => array('' => '', 'vkontakte' => 'vkontakte', 'facebook' => 'facebook', 'twitter' => 'twitter', 'odnoklassniki' => 'odnoklassniki', 'gmail' => 'gmail', 'mailru' => 'mailru', 'live_journal' => 'live_journal', 'yandex' => 'yandex'))),
      'user_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'content'    => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'core_id'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'source_id'  => new sfValidatorPass(array('required' => false)),
      'type'       => new sfValidatorChoice(array('required' => false, 'choices' => array('vkontakte' => 'vkontakte', 'facebook' => 'facebook', 'twitter' => 'twitter', 'odnoklassniki' => 'odnoklassniki', 'gmail' => 'gmail', 'mailru' => 'mailru', 'live_journal' => 'live_journal', 'yandex' => 'yandex'))),
      'user_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'content'    => new sfValidatorPass(array('required' => false)),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'core_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'         => 'Number',
      'source_id'  => 'Text',
      'type'       => 'Enum',
      'user_id'    => 'ForeignKey',
      'content'    => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'core_id'    => 'Number',
    );
  }
}
