<?php

/**
 * UserProductNotice form base class.
 *
 * @method UserProductNotice getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserProductNoticeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'    => new sfWidgetFormInputText(),
      'type'       => new sfWidgetFormInputHidden(),
      'email'      => new sfWidgetFormInputHidden(),
      'product_id' => new sfWidgetFormInputHidden(),
      'user_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'core_id'    => new sfValidatorInteger(array('required' => false)),
      'type'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('type')), 'empty_value' => $this->getObject()->get('type'), 'required' => false)),
      'email'      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('email')), 'empty_value' => $this->getObject()->get('email'), 'required' => false)),
      'product_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_id')), 'empty_value' => $this->getObject()->get('product_id'), 'required' => false)),
      'user_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('user_product_notice[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserProductNotice';
  }

}
