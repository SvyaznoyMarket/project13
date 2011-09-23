<?php

/**
 * ProductType filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'               => new sfWidgetFormFilterInput(),
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'rating_type_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RatingType'), 'add_empty' => true)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'tag_group_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'TagGroup')),
      'service_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory')),
      'product_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
      'property_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty')),
      'property_group_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup')),
    ));

    $this->setValidators(array(
      'core_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'                  => new sfValidatorPass(array('required' => false)),
      'rating_type_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('RatingType'), 'column' => 'id')),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'tag_group_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'TagGroup', 'required' => false)),
      'service_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory', 'required' => false)),
      'product_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
      'property_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty', 'required' => false)),
      'property_group_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addTagGroupListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.TagGroupProductTypeRelation TagGroupProductTypeRelation')
      ->andWhereIn('TagGroupProductTypeRelation.tag_group_id', $values)
    ;
  }

  public function addServiceCategoryListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('ServiceCategoryProductTypeRelation.category_id', $values)
    ;
  }

  public function addProductCategoryListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('ProductCategoryTypeRelation.product_category_id', $values)
    ;
  }

  public function addPropertyListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('ProductTypePropertyRelation.property_id', $values)
    ;
  }

  public function addPropertyGroupListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.ProductTypePropertyGroupRelation ProductTypePropertyGroupRelation')
      ->andWhereIn('ProductTypePropertyGroupRelation.property_group_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ProductType';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'core_id'               => 'Number',
      'name'                  => 'Text',
      'rating_type_id'        => 'ForeignKey',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'tag_group_list'        => 'ManyKey',
      'service_category_list' => 'ManyKey',
      'product_category_list' => 'ManyKey',
      'property_list'         => 'ManyKey',
      'property_group_list'   => 'ManyKey',
    );
  }
}
