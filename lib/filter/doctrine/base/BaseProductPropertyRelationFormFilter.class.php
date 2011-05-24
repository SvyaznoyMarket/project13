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
      'name'         => new sfWidgetFormFilterInput(),
      'property_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => true)),
      'product_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => true)),
      'option_id'    => new sfWidgetFormFilterInput(),
      'value_string' => new sfWidgetFormFilterInput(),
      'value_text'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'         => new sfValidatorPass(array('required' => false)),
      'property_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Property'), 'column' => 'id')),
      'product_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Product'), 'column' => 'id')),
      'option_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'value_string' => new sfValidatorPass(array('required' => false)),
      'value_text'   => new sfValidatorPass(array('required' => false)),
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
      'id'           => 'Number',
      'name'         => 'Text',
      'property_id'  => 'ForeignKey',
      'product_id'   => 'ForeignKey',
      'option_id'    => 'Number',
      'value_string' => 'Text',
      'value_text'   => 'Text',
    );
  }
}
