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
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'rating_type_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RatingType'), 'add_empty' => true)),
      'service_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory')),
      'product_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
      'property_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty')),
    ));

    $this->setValidators(array(
      'name'                  => new sfValidatorPass(array('required' => false)),
      'rating_type_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('RatingType'), 'column' => 'id')),
      'service_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory', 'required' => false)),
      'product_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
      'property_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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

  public function getModelName()
  {
    return 'ProductType';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'name'                  => 'Text',
      'rating_type_id'        => 'ForeignKey',
      'service_category_list' => 'ManyKey',
      'product_category_list' => 'ManyKey',
      'property_list'         => 'ManyKey',
    );
  }
}
