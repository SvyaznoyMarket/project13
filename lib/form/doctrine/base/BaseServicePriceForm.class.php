<?php

/**
 * ServicePrice form base class.
 *
 * @method ServicePrice getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServicePriceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'region_id'  => new sfWidgetFormInputHidden(),
      'service_id' => new sfWidgetFormInputHidden(),
      'price'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'region_id'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('region_id')), 'empty_value' => $this->getObject()->get('region_id'), 'required' => false)),
      'service_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('service_id')), 'empty_value' => $this->getObject()->get('service_id'), 'required' => false)),
      'price'      => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('service_price[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicePrice';
  }

}
