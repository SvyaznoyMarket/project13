<?php

/**
 * ProductHelperFilter filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductHelperFilterFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'value'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'value'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_helper_filter_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductHelperFilter';
  }

  public function getFields()
  {
    return array(
      'answer_id'         => 'Number',
      'product_filter_id' => 'Number',
      'value'             => 'Text',
    );
  }
}
