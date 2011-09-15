<?php

/**
 * Region filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRegionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id' => new sfWidgetFormFilterInput(),
      'root_id' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'     => new sfWidgetFormFilterInput(),
      'rgt'     => new sfWidgetFormFilterInput(),
      'level'   => new sfWidgetFormFilterInput(),
      'token'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'core_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'root_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'   => new sfValidatorPass(array('required' => false)),
      'name'    => new sfValidatorPass(array('required' => false)),
      'type'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('region_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Region';
  }

  public function getFields()
  {
    return array(
      'id'      => 'Number',
      'core_id' => 'Number',
      'root_id' => 'Number',
      'lft'     => 'Number',
      'rgt'     => 'Number',
      'level'   => 'Number',
      'token'   => 'Text',
      'name'    => 'Text',
      'type'    => 'Text',
    );
  }
}
