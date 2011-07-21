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
      'id'                 => new sfWidgetFormInputHidden(),
      'name'               => new sfWidgetFormInputText(),
      'type'               => new sfWidgetFormChoice(array('choices' => array('string' => 'string', 'select' => 'select', 'integer' => 'integer', 'float' => 'float', 'text' => 'text'))),
      'is_multiple'        => new sfWidgetFormInputCheckbox(),
      'unit'               => new sfWidgetFormInputText(),
      'pattern'            => new sfWidgetFormInputText(),
      'product_type_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'group_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup')),
      'product_group_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductGroup')),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'               => new sfValidatorString(array('max_length' => 255)),
      'type'               => new sfValidatorChoice(array('choices' => array(0 => 'string', 1 => 'select', 2 => 'integer', 3 => 'float', 4 => 'text'), 'required' => false)),
      'is_multiple'        => new sfValidatorBoolean(array('required' => false)),
      'unit'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'pattern'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'product_type_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'group_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup', 'required' => false)),
      'product_group_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductGroup', 'required' => false)),
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

    if (isset($this->widgetSchema['group_list']))
    {
      $this->setDefault('group_list', $this->object->Group->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['product_group_list']))
    {
      $this->setDefault('product_group_list', $this->object->ProductGroup->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductTypeList($con);
    $this->saveGroupList($con);
    $this->saveProductGroupList($con);

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

  public function saveGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Group->getPrimaryKeys();
    $values = $this->getValue('group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Group', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Group', array_values($link));
    }
  }

  public function saveProductGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ProductGroup->getPrimaryKeys();
    $values = $this->getValue('product_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ProductGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ProductGroup', array_values($link));
    }
  }

}
