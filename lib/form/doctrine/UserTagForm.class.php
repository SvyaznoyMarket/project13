<?php

/**
 * UserTag form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserTagForm extends BaseUserTagForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema->setLabels(array(
      'name' => 'Название',
    ));

    $this->useFields(array(
      'name',
    ));

    $this->widgetSchema->setNameFormat('tag[%s]');
  }
}
