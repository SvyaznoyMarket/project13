<?php

/**
 * Task filter form.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TaskFormFilter extends BaseTaskFormFilter
{
  public function configure()
  {
    $this->widgetSchema['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));
  }
}
