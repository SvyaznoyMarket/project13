<?php

/**
 * task module configuration.
 *
 * @package    enter
 * @subpackage task
 * @author     Связной Маркет
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class taskGeneratorConfiguration extends BaseTaskGeneratorConfiguration
{
  public function getFilterDefaults()
  {
    return array(
      'status'     => 'run',
      //'created_at' => array('from' => date('Y-m-d 00:00:00'), 'to' => date('Y-m-d 23:59:59')),
    );
  }
}
