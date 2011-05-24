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
      'name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'property_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty')),
    ));

    $this->setValidators(array(
      'name'          => new sfValidatorPass(array('required' => false)),
      'property_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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
      'id'            => 'Number',
      'name'          => 'Text',
      'property_list' => 'ManyKey',
    );
  }
}
