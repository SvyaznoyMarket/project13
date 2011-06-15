<?php

/**
 * ProductHelperFilter form base class.
 *
 * @method ProductHelperFilter getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductHelperFilterForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'answer_id'           => new sfWidgetFormInputHidden(),
      'product_property_id' => new sfWidgetFormInputHidden(),
      'value'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'answer_id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('answer_id')), 'empty_value' => $this->getObject()->get('answer_id'), 'required' => false)),
      'product_property_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_property_id')), 'empty_value' => $this->getObject()->get('product_property_id'), 'required' => false)),
      'value'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_helper_filter[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductHelperFilter';
  }

}
