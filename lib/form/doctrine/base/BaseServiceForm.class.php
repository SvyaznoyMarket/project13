<?php

/**
 * Service form base class.
 *
 * @method Service getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServiceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'token'         => new sfWidgetFormInputText(),
      'name'          => new sfWidgetFormInputText(),
      'description'   => new sfWidgetFormTextarea(),
      'work'          => new sfWidgetFormTextarea(),
      'expendable'    => new sfWidgetFormTextarea(),
      'is_active'     => new sfWidgetFormInputCheckbox(),
      'core_id'       => new sfWidgetFormInputText(),
      'category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory')),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'         => new sfValidatorString(array('max_length' => 255)),
      'name'          => new sfValidatorString(array('max_length' => 255)),
      'description'   => new sfValidatorString(array('required' => false)),
      'work'          => new sfValidatorString(array('required' => false)),
      'expendable'    => new sfValidatorString(array('required' => false)),
      'is_active'     => new sfValidatorBoolean(array('required' => false)),
      'core_id'       => new sfValidatorInteger(array('required' => false)),
      'category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Service', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('service[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Service';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['category_list']))
    {
      $this->setDefault('category_list', $this->object->Category->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveCategoryList($con);

    parent::doSave($con);
  }

  public function saveCategoryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['category_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Category->getPrimaryKeys();
    $values = $this->getValue('category_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Category', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Category', array_values($link));
    }
  }

}
