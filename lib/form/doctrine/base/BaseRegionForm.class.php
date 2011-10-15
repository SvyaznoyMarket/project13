<?php

/**
 * Region form base class.
 *
 * @method Region getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRegionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'core_id'               => new sfWidgetFormInputText(),
      'core_parent_id'        => new sfWidgetFormInputText(),
      'root_id'               => new sfWidgetFormInputText(),
      'product_price_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PriceList'), 'add_empty' => true)),
      'stock_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Stock'), 'add_empty' => false)),
      'lft'                   => new sfWidgetFormInputText(),
      'rgt'                   => new sfWidgetFormInputText(),
      'level'                 => new sfWidgetFormInputText(),
      'token'                 => new sfWidgetFormInputText(),
      'name'                  => new sfWidgetFormInputText(),
      'type'                  => new sfWidgetFormInputText(),
      'is_default'            => new sfWidgetFormInputCheckbox(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'               => new sfValidatorInteger(array('required' => false)),
      'core_parent_id'        => new sfValidatorInteger(array('required' => false)),
      'root_id'               => new sfValidatorInteger(),
      'product_price_list_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PriceList'), 'required' => false)),
      'stock_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Stock'))),
      'lft'                   => new sfValidatorInteger(array('required' => false)),
      'rgt'                   => new sfValidatorInteger(array('required' => false)),
      'level'                 => new sfValidatorInteger(array('required' => false)),
      'token'                 => new sfValidatorString(array('max_length' => 255)),
      'name'                  => new sfValidatorString(array('max_length' => 255)),
      'type'                  => new sfValidatorPass(array('required' => false)),
      'is_default'            => new sfValidatorBoolean(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Region', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('region[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Region';
  }

}
