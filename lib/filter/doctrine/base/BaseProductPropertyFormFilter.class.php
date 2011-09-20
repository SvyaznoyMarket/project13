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
      'core_id'            => new sfWidgetFormFilterInput(),
      'name'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'               => new sfWidgetFormChoice(array('choices' => array('' => '', 'string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'unit'               => new sfWidgetFormFilterInput(),
      'pattern'            => new sfWidgetFormFilterInput(),
      'description'        => new sfWidgetFormFilterInput(),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'product_type_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'group_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup')),
      'product_group_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductGroup')),
    ));

    $this->setValidators(array(
      'core_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'               => new sfValidatorPass(array('required' => false)),
      'type'               => new sfValidatorChoice(array('required' => false, 'choices' => array('string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'unit'               => new sfValidatorPass(array('required' => false)),
      'pattern'            => new sfValidatorPass(array('required' => false)),
      'description'        => new sfValidatorPass(array('required' => false)),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'product_type_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'group_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup', 'required' => false)),
      'product_group_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductGroup', 'required' => false)),
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

  public function addProductGroupListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.ProductGroupPropertyRelation ProductGroupPropertyRelation')
      ->andWhereIn('ProductGroupPropertyRelation.product_group_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ProductProperty';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'core_id'            => 'Number',
      'name'               => 'Text',
      'type'               => 'Enum',
      'is_multiple'        => 'Boolean',
      'unit'               => 'Text',
      'pattern'            => 'Text',
      'description'        => 'Text',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
      'product_type_list'  => 'ManyKey',
      'group_list'         => 'ManyKey',
      'product_group_list' => 'ManyKey',
    );
  }
}
