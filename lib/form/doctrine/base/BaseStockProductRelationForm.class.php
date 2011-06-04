<?php

/**
 * StockProductRelation form base class.
 *
 * @method StockProductRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStockProductRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'stock_id'   => new sfWidgetFormInputHidden(),
      'product_id' => new sfWidgetFormInputHidden(),
      'count'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'stock_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('stock_id')), 'empty_value' => $this->getObject()->get('stock_id'), 'required' => false)),
      'product_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_id')), 'empty_value' => $this->getObject()->get('product_id'), 'required' => false)),
      'count'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('stock_product_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StockProductRelation';
  }

}
