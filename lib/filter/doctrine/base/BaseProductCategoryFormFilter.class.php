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
      'token'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'filter_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'token'           => new sfValidatorPass(array('required' => false)),
      'name'            => new sfValidatorPass(array('required' => false)),
      'filter_group_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('FilterGroup'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('product_category_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductCategory';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'token'           => 'Text',
      'name'            => 'Text',
      'filter_group_id' => 'ForeignKey',
    );
  }
}
