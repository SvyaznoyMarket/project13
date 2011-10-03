<?php

/**
 * ProductCategory filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductCategoryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'           => new sfWidgetFormFilterInput(),
      'core_parent_id'    => new sfWidgetFormFilterInput(),
      'root_id'           => new sfWidgetFormFilterInput(),
      'lft'               => new sfWidgetFormFilterInput(),
      'rgt'               => new sfWidgetFormFilterInput(),
      'level'             => new sfWidgetFormFilterInput(),
      'token'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'filter_group_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'add_empty' => true)),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'product_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Product')),
      'news_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'News')),
    ));

    $this->setValidators(array(
      'core_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'core_parent_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'root_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'             => new sfValidatorPass(array('required' => false)),
      'name'              => new sfValidatorPass(array('required' => false)),
      'filter_group_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('FilterGroup'), 'column' => 'id')),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'product_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Product', 'required' => false)),
      'news_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'News', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_category_filters[%s]');

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
      ->leftJoin($query->getRootAlias().'.ProductCategoryTypeRelation ProductCategoryTypeRelation')
      ->andWhereIn('ProductCategoryTypeRelation.product_type_id', $values)
    ;
  }

  public function addProductListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.ProductCategoryProductRelation ProductCategoryProductRelation')
      ->andWhereIn('ProductCategoryProductRelation.product_id', $values)
    ;
  }

  public function addNewsListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.NewsProductCategoryRelation NewsProductCategoryRelation')
      ->andWhereIn('NewsProductCategoryRelation.news_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ProductCategory';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'core_id'           => 'Number',
      'core_parent_id'    => 'Number',
      'root_id'           => 'Number',
      'lft'               => 'Number',
      'rgt'               => 'Number',
      'level'             => 'Number',
      'token'             => 'Text',
      'name'              => 'Text',
      'filter_group_id'   => 'ForeignKey',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
      'product_type_list' => 'ManyKey',
      'product_list'      => 'ManyKey',
      'news_list'         => 'ManyKey',
    );
  }
}
