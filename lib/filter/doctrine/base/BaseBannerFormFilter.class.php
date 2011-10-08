<?php

/**
 * Banner filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseBannerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'slot_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Slot'), 'add_empty' => true)),
      'token'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'          => new sfWidgetFormFilterInput(),
      'link'          => new sfWidgetFormFilterInput(),
      'image'         => new sfWidgetFormFilterInput(),
      'image_preview' => new sfWidgetFormFilterInput(),
      'position'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'slot_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Slot'), 'column' => 'id')),
      'token'         => new sfValidatorPass(array('required' => false)),
      'name'          => new sfValidatorPass(array('required' => false)),
      'link'          => new sfValidatorPass(array('required' => false)),
      'image'         => new sfValidatorPass(array('required' => false)),
      'image_preview' => new sfValidatorPass(array('required' => false)),
      'position'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('banner_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Banner';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'slot_id'       => 'ForeignKey',
      'token'         => 'Text',
      'name'          => 'Text',
      'link'          => 'Text',
      'image'         => 'Text',
      'image_preview' => 'Text',
      'position'      => 'Number',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
    );
  }
}
