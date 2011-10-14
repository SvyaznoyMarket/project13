<?php

/**
 * banner components.
 *
 * @package    enter
 * @subpackage banner
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bannerComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param Slot $slot Слот
  */
  public function executeShow()
  {
    if (!$this->slot instanceof Slot)
    {
      return sfView::NONE;
    }
    $this->view = preg_replace('/^banner_/', '', $this->slot->token);

    $list = array();
    foreach (BannerTable::getInstance()->getListBySlot($this->slot) as $banner)
    {
      $list[] = array(
        'name'          => $banner->name,
        'url'           => $banner->link,
        'image'         => $banner->image,
        'image_preview' => $banner->image_preview,
      );
    }

    $this->setVar('list', $list, true);
  }
}
