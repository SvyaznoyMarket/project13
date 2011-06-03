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
      'root_id'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'             => new sfWidgetFormFilterInput(),
      'rgt'             => new sfWidgetFormFilterInput(),
      'level'           => new sfWidgetFormFilterInput(),
      'token'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'filter_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'root_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'root_id'         => 'Number',
      'lft'             => 'Number',
      'rgt'             => 'Number',
      'level'           => 'Number',
      'token'           => 'Text',
      'name'            => 'Text',
      'filter_group_id' => 'ForeignKey',
    );
  }
}
