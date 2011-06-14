<?php

/**
 * News filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseNewsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'category_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => true)),
      'token'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'preview'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'body'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'published_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'is_active'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'position'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'product_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Product')),
      'product_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
      'creator_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Creator')),
    ));

    $this->setValidators(array(
      'category_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Category'), 'column' => 'id')),
      'token'                 => new sfValidatorPass(array('required' => false)),
      'name'                  => new sfValidatorPass(array('required' => false)),
      'preview'               => new sfValidatorPass(array('required' => false)),
      'body'                  => new sfValidatorPass(array('required' => false)),
      'published_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'is_active'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'position'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'product_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Product', 'required' => false)),
      'product_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
      'creator_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Creator', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('news_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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
      ->leftJoin($query->getRootAlias().'.NewsProductRelation NewsProductRelation')
      ->andWhereIn('NewsProductRelation.product_id', $values)
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
      ->leftJoin($query->getRootAlias().'.NewsProductCategoryRelation NewsProductCategoryRelation')
      ->andWhereIn('NewsProductCategoryRelation.product_category_id', $values)
    ;
  }

  public function addCreatorListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.NewsCreatorRelation NewsCreatorRelation')
      ->andWhereIn('NewsCreatorRelation.creator_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'News';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'category_id'           => 'ForeignKey',
      'token'                 => 'Text',
      'name'                  => 'Text',
      'preview'               => 'Text',
      'body'                  => 'Text',
      'published_at'          => 'Date',
      'is_active'             => 'Boolean',
      'position'              => 'Number',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'product_list'          => 'ManyKey',
      'product_category_list' => 'ManyKey',
      'creator_list'          => 'ManyKey',
    );
  }
}
