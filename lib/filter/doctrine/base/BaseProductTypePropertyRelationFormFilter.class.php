<?php

/**
 * ProductTypePropertyRelation filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductTypePropertyRelationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'            => new sfWidgetFormFilterInput(),
      'group_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'position'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'group_position'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'view_show'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'view_list'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'name'            => new sfValidatorPass(array('required' => false)),
      'group_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Group'), 'column' => 'id')),
      'position'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'group_position'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'view_show'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'view_list'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('product_type_property_relation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductTypePropertyRelation';
  }

  public function getFields()
  {
    return array(
      'name'            => 'Text',
      'product_type_id' => 'Number',
      'property_id'     => 'Number',
      'group_id'        => 'ForeignKey',
      'position'        => 'Number',
      'group_position'  => 'Number',
      'view_show'       => 'Boolean',
      'view_list'       => 'Boolean',
    );
  }
}
