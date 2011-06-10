<?php

/**
 * Product form base class.
 *
 * @method Product getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'token'       => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'type_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'add_empty' => false)),
      'creator_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Creator'), 'add_empty' => false)),
      'category_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => false)),
      'view_show'   => new sfWidgetFormInputCheckbox(),
      'view_list'   => new sfWidgetFormInputCheckbox(),
      'is_instock'  => new sfWidgetFormInputCheckbox(),
      'description' => new sfWidgetFormInputText(),
      'rating'      => new sfWidgetFormInputText(),
      'price'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'       => new sfValidatorString(array('max_length' => 255)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'type_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Type'))),
      'creator_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Creator'))),
      'category_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Category'))),
      'view_show'   => new sfValidatorBoolean(array('required' => false)),
      'view_list'   => new sfValidatorBoolean(array('required' => false)),
      'is_instock'  => new sfValidatorBoolean(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'rating'      => new sfValidatorNumber(array('required' => false)),
      'price'       => new sfValidatorNumber(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Product', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

}
