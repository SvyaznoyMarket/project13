<?php

/**
 * qrcode actions.
 *
 * @package    enter
 * @subpackage qrcode
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class qrcodeActions extends myActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404If(empty($request['qrcode']));
    $qrcode = RepositoryManager::getQrcode()->getByQrcode($request['qrcode']);

    $idList = array();
    foreach ($qrcode->getItemList() as $item)
    {
      $this->forward404Unless($item->isProduct(), 'Этот тип qrcode не поддерживается');
      $idList[] = $item->getId();
    }
    $this->forward404Unless(count($idList) > 0, 'Нет объектов для отображения');

    switch (count($idList))
    {
      case 0:
        $this->forward404('Нет объектов для отображения');
        break;
      case 1:
        $request->setParameter('product', $idList[0]);
        $this->forward('productCard_', 'index');
        break;
      default:
        $request->setParameter('products', $idList);
        $this->forward('product_', 'list');
        break;
    }
  }
}