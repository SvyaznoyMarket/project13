<?php

/**
 * ProductGroupPropertyRelation filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductGroupPropertyRelationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'property_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => true)),
      'product_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductGroup'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'property_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Property'), 'column' => 'id')),
      'product_group_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProductGroup'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('product_group_property_relation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductGroupPropertyRelation';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'property_id'      => 'ForeignKey',
      'product_group_id' => 'ForeignKey',
    );
  }
}
