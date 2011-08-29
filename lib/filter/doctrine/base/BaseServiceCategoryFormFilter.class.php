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
      'root_id'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'       => new sfWidgetFormFilterInput(),
      'rgt'       => new sfWidgetFormFilterInput(),
      'level'     => new sfWidgetFormFilterInput(),
      'token'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_active' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'root_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'     => new sfValidatorPass(array('required' => false)),
      'name'      => new sfValidatorPass(array('required' => false)),
      'is_active' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('service_category_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServiceCategory';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'root_id'   => 'Number',
      'lft'       => 'Number',
      'rgt'       => 'Number',
      'level'     => 'Number',
      'token'     => 'Text',
      'name'      => 'Text',
      'is_active' => 'Boolean',
    );
  }
}
