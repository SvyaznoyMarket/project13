<?php

/**
 * News form base class.
 *
 * @method News getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNewsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'category_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => false)),
      'token'                 => new sfWidgetFormInputText(),
      'name'                  => new sfWidgetFormInputText(),
      'preview'               => new sfWidgetFormTextarea(),
      'body'                  => new sfWidgetFormTextarea(),
      'published_at'          => new sfWidgetFormDateTime(),
      'is_active'             => new sfWidgetFormInputCheckbox(),
      'position'              => new sfWidgetFormInputText(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'product_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Product')),
      'product_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
      'creator_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Creator')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'category_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Category'))),
      'token'                 => new sfValidatorString(array('max_length' => 255)),
      'name'                  => new sfValidatorString(array('max_length' => 255)),
      'preview'               => new sfValidatorString(array('max_length' => 500)),
      'body'                  => new sfValidatorString(),
      'published_at'          => new sfValidatorDateTime(array('required' => false)),
      'is_active'             => new sfValidatorBoolean(array('required' => false)),
      'position'              => new sfValidatorInteger(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'product_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Product', 'required' => false)),
      'product_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
      'creator_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Creator', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'News', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('news[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'News';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['product_list']))
    {
      $this->setDefault('product_list', $this->object->Product->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['product_category_list']))
    {
      $this->setDefault('product_category_list', $this->object->ProductCategory->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['creator_list']))
    {
      $this->setDefault('creator_list', $this->object->Creator->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductList($con);
    $this->saveProductCategoryList($con);
    $this->saveCreatorList($con);

    parent::doSave($con);
  }

  public function saveProductList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Product->getPrimaryKeys();
    $values = $this->getValue('product_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Product', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Product', array_values($link));
    }
  }

  public function saveProductCategoryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_category_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ProductCategory->getPrimaryKeys();
    $values = $this->getValue('product_category_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ProductCategory', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ProductCategory', array_values($link));
    }
  }

  public function saveCreatorList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['creator_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Creator->getPrimaryKeys();
    $values = $this->getValue('creator_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Creator', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Creator', array_values($link));
    }
  }

}
