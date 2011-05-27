<?php

/**
 * User filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'login'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'first_name'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'last_name'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'middle_name' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'photo'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'login'       => new sfValidatorPass(array('required' => false)),
      'first_name'  => new sfValidatorPass(array('required' => false)),
      'last_name'   => new sfValidatorPass(array('required' => false)),
      'middle_name' => new sfValidatorPass(array('required' => false)),
      'photo'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'User';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'login'       => 'Text',
      'first_name'  => 'Text',
      'last_name'   => 'Text',
      'middle_name' => 'Text',
      'photo'       => 'Text',
    );
  }
}
