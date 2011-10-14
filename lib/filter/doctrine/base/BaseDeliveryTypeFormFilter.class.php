<?php

/**
 * DeliveryType filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDeliveryTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'     => new sfWidgetFormFilterInput(),
      'token'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_active'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'core_id'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'       => new sfValidatorPass(array('required' => false)),
      'name'        => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'is_active'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('delivery_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DeliveryType';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'core_id'     => 'Number',
      'token'       => 'Text',
      'name'        => 'Text',
      'description' => 'Text',
      'is_active'   => 'Boolean',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
    );
  }
}
