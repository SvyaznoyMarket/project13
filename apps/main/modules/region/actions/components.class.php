<?php

/**
 * page components.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class regionComponents extends myComponents
{

  public function executeSelect()
  {
    $list = array();

    $region = $this->getUser()->getRegion();
    $regionTable = RegionTable::getInstance();

    $active['name'] = $region['name'];
    $active['url'] = url_for('region_change', $regionTable->getById($region['id']));
    foreach (RegionTable::getInstance()->getCityList(array(
      'hydrate_array' => true,
      'select'        => 'region.id, region.name, region.token')
    ) as $item) {
      if ($active['name'] == $item['name']) continue;
      $list[] = array(
        'url'  => url_for('region_change', array('region' => $item['token'])),
        'name' => $item['name'],
      );
    }

    $this->setVar('list', $list, true);
    $this->setVar('active', $active, true);
  }

}
