<?php

/**
 * Slot filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSlotFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'type'       => new sfWidgetFormChoice(array('choices' => array('' => '', 'menu' => 'menu', 'banner' => 'banner'))),
      'token'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'       => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'type'       => new sfValidatorChoice(array('required' => false, 'choices' => array('menu' => 'menu', 'banner' => 'banner'))),
      'token'      => new sfValidatorPass(array('required' => false)),
      'name'       => new sfValidatorPass(array('required' => false)),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('slot_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Slot';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'type'       => 'Enum',
      'token'      => 'Text',
      'name'       => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    );
  }
}
