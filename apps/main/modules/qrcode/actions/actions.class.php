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
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $qrcode = !empty($request['qrcode']) ? QrcodeTable::getInstance()->findOneByToken($request['qrcode']) : false;
    //$this->forward404Unless($qrcode, "Не найден qrcode #{$request['qrcode']}");
    $this->redirectUnless($qrcode, '@homepage');

    $ids = array();
    foreach ($qrcode->getContentData() as $item)
    {
      $ids[] = $item['id'];
    }
    $this->forward404Unless(count($ids) > 0, 'Нет объектов для отображения');

    $view = $qrcode->getView();
    switch ($view)
    {
      case 'product.list';
        //$tokens = ProductTable::getInstance()->getTokensByIds($ids);
        //$request->setParameter('products', $tokens);

        $coreIds = ProductTable::getInstance()->getCoreIdsByIds($ids);
        $request->setParameter('products', $coreIds);
        $this->forward('product', 'list');
        break;
      case 'product.show':
        $product = ProductTable::getInstance()->getById($ids[0]);
        $request->setParameter('product', $product);
        $this->forward('productCard', 'index');
        break;
    }

    $this->forward404('Объекты не найдены');
  }
}