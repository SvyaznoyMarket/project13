<?php

/**
 * Stock form base class.
 *
 * @method Stock getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStockForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'token'     => new sfWidgetFormInputText(),
      'name'      => new sfWidgetFormInputText(),
      'type'      => new sfWidgetFormChoice(array('choices' => array('main' => 'main', 'region' => 'region', 'shop' => 'shop', 'provider' => 'provider'))),
      'region_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => false)),
      'shop_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'     => new sfValidatorString(array('max_length' => 255)),
      'name'      => new sfValidatorString(array('max_length' => 255)),
      'type'      => new sfValidatorChoice(array('choices' => array(0 => 'main', 1 => 'region', 2 => 'shop', 3 => 'provider'))),
      'region_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Region'))),
      'shop_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Stock', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('stock[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Stock';
  }

}
