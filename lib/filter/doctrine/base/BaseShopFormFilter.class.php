<?php

/**
 * Shop filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseShopFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'      => new sfWidgetFormFilterInput(),
      'region_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'token'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'latitude'     => new sfWidgetFormFilterInput(),
      'longitude'    => new sfWidgetFormFilterInput(),
      'regime'       => new sfWidgetFormFilterInput(),
      'address'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'phonenumbers' => new sfWidgetFormFilterInput(),
      'photo'        => new sfWidgetFormFilterInput(),
      'description'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'core_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'region_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Region'), 'column' => 'id')),
      'token'        => new sfValidatorPass(array('required' => false)),
      'name'         => new sfValidatorPass(array('required' => false)),
      'latitude'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'regime'       => new sfValidatorPass(array('required' => false)),
      'address'      => new sfValidatorPass(array('required' => false)),
      'phonenumbers' => new sfValidatorPass(array('required' => false)),
      'photo'        => new sfValidatorPass(array('required' => false)),
      'description'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('shop_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Shop';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'core_id'      => 'Number',
      'region_id'    => 'ForeignKey',
      'token'        => 'Text',
      'name'         => 'Text',
      'latitude'     => 'Number',
      'longitude'    => 'Number',
      'regime'       => 'Text',
      'address'      => 'Text',
      'phonenumbers' => 'Text',
      'photo'        => 'Text',
      'description'  => 'Text',
    );
  }
}
