<?php

/**
 * SimilarProductGroup filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductGroupFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'product_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'), 'add_empty' => true)),
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'products'        => new sfWidgetFormFilterInput(),
      'match'           => new sfWidgetFormFilterInput(),
      'price'           => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'product_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProductType'), 'column' => 'id')),
      'name'            => new sfValidatorPass(array('required' => false)),
      'products'        => new sfValidatorPass(array('required' => false)),
      'match'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'price'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('similar_product_group_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProductGroup';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'product_type_id' => 'ForeignKey',
      'name'            => 'Text',
      'products'        => 'Text',
      'match'           => 'Number',
      'price'           => 'Number',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
