<?php

/**
 * Stock filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStockFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'      => new sfWidgetFormChoice(array('choices' => array('' => '', 'main' => 'main', 'region' => 'region', 'shop' => 'shop', 'provider' => 'provider'))),
      'region_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'shop_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'token'     => new sfValidatorPass(array('required' => false)),
      'name'      => new sfValidatorPass(array('required' => false)),
      'type'      => new sfValidatorChoice(array('required' => false, 'choices' => array('main' => 'main', 'region' => 'region', 'shop' => 'shop', 'provider' => 'provider'))),
      'region_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Region'), 'column' => 'id')),
      'shop_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Shop'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('stock_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Stock';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'token'     => 'Text',
      'name'      => 'Text',
      'type'      => 'Enum',
      'region_id' => 'ForeignKey',
      'shop_id'   => 'ForeignKey',
    );
  }
}
