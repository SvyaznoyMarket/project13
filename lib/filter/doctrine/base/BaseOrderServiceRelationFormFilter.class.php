<?php

/**
 * OrderServiceRelation filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseOrderServiceRelationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'order_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Order'), 'add_empty' => true)),
      'product_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => true)),
      'service_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Service'), 'add_empty' => true)),
      'price'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quantity'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'order_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Order'), 'column' => 'id')),
      'product_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Product'), 'column' => 'id')),
      'service_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Service'), 'column' => 'id')),
      'price'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'quantity'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('order_service_relation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrderServiceRelation';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'order_id'   => 'ForeignKey',
      'product_id' => 'ForeignKey',
      'service_id' => 'ForeignKey',
      'price'      => 'Number',
      'quantity'   => 'Number',
    );
  }
}
