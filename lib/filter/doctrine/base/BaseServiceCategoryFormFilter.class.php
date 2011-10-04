<?php

/**
 * ServiceCategory filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServiceCategoryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'root_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'core_parent_id'    => new sfWidgetFormFilterInput(),
      'lft'               => new sfWidgetFormFilterInput(),
      'rgt'               => new sfWidgetFormFilterInput(),
      'level'             => new sfWidgetFormFilterInput(),
      'token'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_active'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'core_id'           => new sfWidgetFormFilterInput(),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
    ));

    $this->setValidators(array(
      'root_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'core_parent_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'             => new sfValidatorPass(array('required' => false)),
      'name'              => new sfValidatorPass(array('required' => false)),
      'is_active'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'core_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('service_category_filters[%s]');

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
      ->leftJoin($query->getRootAlias().'.ServiceCategoryProductTypeRelation ServiceCategoryProductTypeRelation')
      ->andWhereIn('ServiceCategoryProductTypeRelation.product_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ServiceCategory';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'root_id'           => 'Number',
      'core_parent_id'    => 'Number',
      'lft'               => 'Number',
      'rgt'               => 'Number',
      'level'             => 'Number',
      'token'             => 'Text',
      'name'              => 'Text',
      'is_active'         => 'Boolean',
      'core_id'           => 'Number',
      'product_type_list' => 'ManyKey',
    );
  }
}
