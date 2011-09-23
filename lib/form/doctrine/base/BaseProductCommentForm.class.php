<?php

/**
 * ProductComment form base class.
 *
 * @method ProductComment getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductCommentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'core_id'        => new sfWidgetFormInputText(),
      'core_parent_id' => new sfWidgetFormInputText(),
      'core_user_id'   => new sfWidgetFormInputText(),
      'product_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => false)),
      'lft'            => new sfWidgetFormInputText(),
      'rgt'            => new sfWidgetFormInputText(),
      'level'          => new sfWidgetFormInputText(),
      'user_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'content'        => new sfWidgetFormInputText(),
      'helpful'        => new sfWidgetFormInputText(),
      'unhelpful'      => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'        => new sfValidatorInteger(array('required' => false)),
      'core_parent_id' => new sfValidatorInteger(array('required' => false)),
      'core_user_id'   => new sfValidatorInteger(array('required' => false)),
      'product_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Product'))),
      'lft'            => new sfValidatorInteger(array('required' => false)),
      'rgt'            => new sfValidatorInteger(array('required' => false)),
      'level'          => new sfValidatorInteger(array('required' => false)),
      'user_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'content'        => new sfValidatorString(array('max_length' => 255)),
      'helpful'        => new sfValidatorInteger(array('required' => false)),
      'unhelpful'      => new sfValidatorInteger(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('product_comment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductComment';
  }

}
