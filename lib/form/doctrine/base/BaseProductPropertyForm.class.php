<?php

/**
 * ProductProperty form base class.
 *
 * @method ProductProperty getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'name'              => new sfWidgetFormInputText(),
      'type'              => new sfWidgetFormChoice(array('choices' => array('string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'       => new sfWidgetFormInputCheckbox(),
      'pattern'           => new sfWidgetFormInputText(),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'              => new sfValidatorString(array('max_length' => 255)),
      'type'              => new sfValidatorChoice(array('choices' => array(0 => 'string', 1 => 'select', 2 => 'integer', 3 => 'float', 4 => 'text'), 'required' => false)),
      'is_multiple'       => new sfValidatorBoolean(array('required' => false)),
      'pattern'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_property[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductProperty';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['product_type_list']))
    {
      $this->setDefault('product_type_list', $this->object->ProductType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductTypeList($con);

    parent::doSave($con);
  }

  public function saveProductTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ProductType->getPrimaryKeys();
    $values = $this->getValue('product_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ProductType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ProductType', array_values($link));
    }
  }

}
