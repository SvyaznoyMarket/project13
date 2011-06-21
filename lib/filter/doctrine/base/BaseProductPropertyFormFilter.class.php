<?php

/**
 * ProductProperty filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'              => new sfWidgetFormChoice(array('choices' => array('' => '', 'string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'pattern'           => new sfWidgetFormFilterInput(),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'group_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup')),
    ));

    $this->setValidators(array(
      'name'              => new sfValidatorPass(array('required' => false)),
      'type'              => new sfValidatorChoice(array('required' => false, 'choices' => array('string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'pattern'           => new sfValidatorPass(array('required' => false)),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'group_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_property_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addProductTypeListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.ProductTypePropertyRelation ProductTypePropertyRelation')
      ->andWhereIn('ProductTypePropertyRelation.product_type_id', $values)
    ;
  }

  public function addGroupListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.ProductTypePropertyRelation ProductTypePropertyRelation')
      ->andWhereIn('ProductTypePropertyRelation.group_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ProductProperty';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'name'              => 'Text',
      'type'              => 'Enum',
      'is_multiple'       => 'Boolean',
      'pattern'           => 'Text',
      'product_type_list' => 'ManyKey',
      'group_list'        => 'ManyKey',
    );
  }
}
