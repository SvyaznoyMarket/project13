<?php

/**
 * Banner form base class.
 *
 * @method Banner getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBannerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'slot_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Slot'), 'add_empty' => false)),
      'token'         => new sfWidgetFormInputText(),
      'name'          => new sfWidgetFormInputText(),
      'link'          => new sfWidgetFormInputText(),
      'image'         => new sfWidgetFormInputText(),
      'image_preview' => new sfWidgetFormInputText(),
      'position'      => new sfWidgetFormInputText(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'slot_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Slot'))),
      'token'         => new sfValidatorString(array('max_length' => 255)),
      'name'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'link'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'image'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'image_preview' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'position'      => new sfValidatorInteger(array('required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Banner', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('banner[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Banner';
  }

}
