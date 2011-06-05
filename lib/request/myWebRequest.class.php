<?php

/**
 * Base project form.
 *
 * @package    enter
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class myWebRequest extends sfWebRequest
{

  public function initialize(sfEventDispatcher $dispatcher, $parameters = array(), $attributes = array(), $options = array())
  {
    parent::initialize($dispatcher, $parameters, $attributes, $options);

    $geoIp = array(
      'region'        => isset($_SERVER['GEOIP_REGION']) ? $_SERVER['GEOIP_REGION'] : null,
      'country_name'  => isset($_SERVER['GEOIP_COUNTRY_NAME']) ? $_SERVER['GEOIP_COUNTRY_NAME'] : null,
      'country_code'  => isset($_SERVER['GEOIP_COUNTRY_CODE']) ? $_SERVER['GEOIP_COUNTRY_CODE'] : null,
      'city_name'     => isset($_SERVER['GEOIP_CITY_NAME']) ? $_SERVER['GEOIP_CITY_NAME'] : null,
      'city_code'     => isset($_SERVER['GEOIP_CITY_CODE']) ? $_SERVER['GEOIP_CITY_CODE'] : null,
      'area_code'     => isset($_SERVER['GEOIP_AREA_CODE']) ? $_SERVER['GEOIP_AREA_CODE'] : null,
      'ip_address'    => isset($_SERVER['GEOIP_ADDR']) ? $_SERVER['GEOIP_ADDR'] : null,
    );

    $this->parameterHolder->add(array('geoip' => $geoIp, ));
  }
}
