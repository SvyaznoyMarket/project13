<?php

class QrcodeRepository
{
  /**
   * @param $qrcode
   * @return QrcodeEntity
   */
  public function getByQrcode($qrcode){
    $data = CoreClient::getInstance()->query('qrcode.get', array('qrcode'=>$qrcode));
    $entity = new QrcodeEntity($data);
    if(!empty($data['item_list'])){
      foreach($data['item_list'] as $itemData){
        $entity->addItem(new QrcodeItemEntity($itemData));
      }
    }
    return $entity;
  }
}
