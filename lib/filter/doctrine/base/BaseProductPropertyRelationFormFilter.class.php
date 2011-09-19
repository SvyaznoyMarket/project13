<?php

/**
 * ProductPropertyRelation filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyRelationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'          => new sfWidgetFormFilterInput(),
      'product_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => true)),
      'property_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => true)),
      'option_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Option'), 'add_empty' => true)),
      'value_integer' => new sfWidgetFormFilterInput(),
      'value_float'   => new sfWidgetFormFilterInput(),
      'value_string'  => new sfWidgetFormFilterInput(),
      'value_text'    => new sfWidgetFormFilterInput(),
      'value'         => new sfWidgetFormFilterInput(),
      'unit'          => new sfWidgetFormFilterInput(),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'          => new sfValidatorPass(array('required' => false)),
      'product_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Product'), 'column' => 'id')),
      'property_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Property'), 'column' => 'id')),
      'option_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Option'), 'column' => 'id')),
      'value_integer' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'value_float'   => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'value_string'  => new sfValidatorPass(array('required' => false)),
      'value_text'    => new sfValidatorPass(array('required' => false)),
      'value'         => new sfValidatorPass(array('required' => false)),
      'unit'          => new sfValidatorPass(array('required' => false)),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('product_property_relation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPropertyRelation';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'name'          => 'Text',
      'product_id'    => 'ForeignKey',
      'property_id'   => 'ForeignKey',
      'option_id'     => 'ForeignKey',
      'value_integer' => 'Number',
      'value_float'   => 'Number',
      'value_string'  => 'Text',
      'value_text'    => 'Text',
      'value'         => 'Text',
      'unit'          => 'Text',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
    );
  }
}
