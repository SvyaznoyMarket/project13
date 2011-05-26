<?php

/**
 * ProductPropertyOption filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyOptionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'property_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => true)),
      'value'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit'        => new sfWidgetFormFilterInput(),
      'position'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'property_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Property'), 'column' => 'id')),
      'value'       => new sfValidatorPass(array('required' => false)),
      'unit'        => new sfValidatorPass(array('required' => false)),
      'position'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('product_property_option_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPropertyOption';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'property_id' => 'ForeignKey',
      'value'       => 'Text',
      'unit'        => 'Text',
      'position'    => 'Number',
    );
  }
}
