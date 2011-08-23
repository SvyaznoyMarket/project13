<?php

/**
 * Order filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseOrderFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'payment_method_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'add_empty' => true)),
      'sum'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'person_type'            => new sfWidgetFormChoice(array('choices' => array('' => '', 'individual' => 'individual', 'legal' => 'legal'))),
      'region_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'receipt_type'           => new sfWidgetFormChoice(array('choices' => array('' => '', 'pickup' => 'pickup', 'delivery' => 'delivery'))),
      'shop_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'add_empty' => true)),
      'delivery_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'add_empty' => true)),
      'delivered_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'address'                => new sfWidgetFormFilterInput(),
      'recipient_first_name'   => new sfWidgetFormFilterInput(),
      'recipient_last_name'    => new sfWidgetFormFilterInput(),
      'recipient_middle_name'  => new sfWidgetFormFilterInput(),
      'recipient_phonenumbers' => new sfWidgetFormFilterInput(),
      'step'                   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'token'                  => new sfValidatorPass(array('required' => false)),
      'user_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'payment_method_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PaymentMethod'), 'column' => 'id')),
      'sum'                    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'person_type'            => new sfValidatorChoice(array('required' => false, 'choices' => array('individual' => 'individual', 'legal' => 'legal'))),
      'region_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Region'), 'column' => 'id')),
      'receipt_type'           => new sfValidatorChoice(array('required' => false, 'choices' => array('pickup' => 'pickup', 'delivery' => 'delivery'))),
      'shop_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Shop'), 'column' => 'id')),
      'delivery_type_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DeliveryType'), 'column' => 'id')),
      'delivered_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'address'                => new sfValidatorPass(array('required' => false)),
      'recipient_first_name'   => new sfValidatorPass(array('required' => false)),
      'recipient_last_name'    => new sfValidatorPass(array('required' => false)),
      'recipient_middle_name'  => new sfValidatorPass(array('required' => false)),
      'recipient_phonenumbers' => new sfValidatorPass(array('required' => false)),
      'step'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('order_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Order';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'token'                  => 'Text',
      'user_id'                => 'ForeignKey',
      'payment_method_id'      => 'ForeignKey',
      'sum'                    => 'Number',
      'person_type'            => 'Enum',
      'region_id'              => 'ForeignKey',
      'receipt_type'           => 'Enum',
      'shop_id'                => 'ForeignKey',
      'delivery_type_id'       => 'ForeignKey',
      'delivered_at'           => 'Date',
      'address'                => 'Text',
      'recipient_first_name'   => 'Text',
      'recipient_last_name'    => 'Text',
      'recipient_middle_name'  => 'Text',
      'recipient_phonenumbers' => 'Text',
      'step'                   => 'Number',
    );
  }
}
