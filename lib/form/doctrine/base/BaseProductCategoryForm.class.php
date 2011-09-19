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
      'id'                => new sfWidgetFormInputHidden(),
      'core_id'           => new sfWidgetFormInputText(),
      'core_parent_id'    => new sfWidgetFormInputText(),
      'root_id'           => new sfWidgetFormInputText(),
      'lft'               => new sfWidgetFormInputText(),
      'rgt'               => new sfWidgetFormInputText(),
      'level'             => new sfWidgetFormInputText(),
      'token'             => new sfWidgetFormInputText(),
      'name'              => new sfWidgetFormInputText(),
      'filter_group_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'add_empty' => true)),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'news_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'News')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'           => new sfValidatorInteger(array('required' => false)),
      'core_parent_id'    => new sfValidatorInteger(array('required' => false)),
      'root_id'           => new sfValidatorInteger(array('required' => false)),
      'lft'               => new sfValidatorInteger(array('required' => false)),
      'rgt'               => new sfValidatorInteger(array('required' => false)),
      'level'             => new sfValidatorInteger(array('required' => false)),
      'token'             => new sfValidatorString(array('max_length' => 255)),
      'name'              => new sfValidatorString(array('max_length' => 255)),
      'filter_group_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('FilterGroup'), 'required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'news_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'News', 'required' => false)),
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

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['product_type_list']))
    {
      $this->setDefault('product_type_list', $this->object->ProductType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['news_list']))
    {
      $this->setDefault('news_list', $this->object->News->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductTypeList($con);
    $this->saveNewsList($con);

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

  public function saveNewsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['news_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->News->getPrimaryKeys();
    $values = $this->getValue('news_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('News', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('News', array_values($link));
    }
  }

}
