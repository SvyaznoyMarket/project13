<?php

/**
 * ProductPrice form base class.
 *
 * @method ProductPrice getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductPriceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'product_price_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PriceList'), 'add_empty' => false)),
      'product_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => false)),
      'price'                 => new sfWidgetFormInputText(),
      'old_price'             => new sfWidgetFormInputText(),
      'avg_price'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'product_price_list_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PriceList'))),
      'product_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Product'))),
      'price'                 => new sfValidatorNumber(array('required' => false)),
      'old_price'             => new sfValidatorNumber(array('required' => false)),
      'avg_price'             => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_price[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPrice';
  }

}
