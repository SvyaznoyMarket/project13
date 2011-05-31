<?php

/**
 * ProductCategory form base class.
 *
 * @method ProductCategory getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'token'           => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'filter_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'           => new sfValidatorString(array('max_length' => 255)),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'filter_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ProductCategory', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('product_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductCategory';
  }

}
