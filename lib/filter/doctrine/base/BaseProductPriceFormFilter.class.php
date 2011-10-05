<?php

/**
 * ProductPrice filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductPriceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'product_price_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PriceList'), 'add_empty' => true)),
      'product_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => true)),
      'price'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'old_price'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'avg_price'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'product_price_list_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PriceList'), 'column' => 'id')),
      'product_id'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Product'), 'column' => 'id')),
      'price'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'old_price'             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'avg_price'             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('product_price_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPrice';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'product_price_list_id' => 'ForeignKey',
      'product_id'            => 'ForeignKey',
      'price'                 => 'Number',
      'old_price'             => 'Number',
      'avg_price'             => 'Number',
    );
  }
}
