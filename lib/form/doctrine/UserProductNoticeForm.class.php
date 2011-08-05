<?php

/**
 * UserProductNotice form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserProductNoticeForm extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
      'choices'  => array('insale' => 'когда появится в продаже', 'price' => 'когда снизится цена', 'comment' => 'когда появится новый отзыв'),
      'expanded' => true,
      'multiple' => true,
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array(
      'choices'  => array('insale', 'price', 'comment'),
      'multiple' => true,
    ));

    $this->widgetSchema['region_id'] = new sfWidgetFormChoice(array('choices' => RegionTable::getInstance()->getChoices()));
    $this->validatorSchema['region_id'] = new sfValidatorDoctrineChoice(array('model' => 'Region'));

    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->validatorSchema['email'] = new sfValidatorEmail();

    $this->widgetSchema->setLabels(array(
      'type'      => 'Получить письмо',
      'region_id' => 'Ваш регион',
      'email'     => 'Email',
    ));

    $this->useFields(array(
      'type',
      'region_id',
      'email',
    ));

    $this->widgetSchema->setNameFormat('notice[%s]');
  }
}
