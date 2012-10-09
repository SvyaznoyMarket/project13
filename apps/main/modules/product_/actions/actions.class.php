<?php

/**
 * product actions.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class product_Actions extends myActions
{
  public function executeList(sfWebRequest $request)
  {
    $idList = is_array($request['products']) ? $request['products'] : explode(',', $request['products']);
    $productList = RepositoryManager::getProduct()->getListById($idList, true);
    $this->forward404Unless(count($productList));
    //init view
    $this->setVar('productList', $productList);
    $request->setParameter('_template', 'product_catalog');
  }

  public function executeSet(sfWebRequest $request)
  {
    $this->getRequest()->setParameter('_template', 'product_catalog');

    $barcodeList = is_array($request['products']) ? $request['products'] : explode(',', $request['products']);

    $this->forward404Unless((bool)$barcodeList);

    $productList = RepositoryManager::getProduct()->getListByBarcode($barcodeList, true);
    $this->forward404Unless((bool)$productList);

    $categoryMap = array();
    foreach($productList as $product){
      if($category = $product->getFinalCategory()){
        $categoryMap[$category->getId()] = $category;
      }
    }

    $this->setVar('productList', $productList);
    $this->setVar('productCategoryList', array_values($categoryMap));
  }

  public function executeDeliveryInfo(sfWebRequest $request)
  {
    $productId = $request->getParameter('product');
    if (!$productId) {
      $productIds = $request->getParameter('ids');
    } else {
      $productIds = array($productId);
    }

    $data = array();
    $now = new DateTime();
    foreach ($productIds as $productId) {
      $productObj = ProductTable::getInstance()->findOneByCoreId($productId);
      if (!$productObj || !$productObj instanceof Doctrine_Record) {
        continue;
      }

      $deliveries = Core::getInstance()->getProductDeliveryData($productId, $this->getUser()->getRegion('core_id'));
      $result = array('success' => true, 'deliveries' => array());
      if (!$deliveries || !count($deliveries) || isset($deliveries['result'])) {
        $deliveries = array(array(
          'mode_id' => 1,
          'date' => date('Y-m-d', time() + (3600 * 48)),
          'price' => null,
        ));
      }
      $deliveryData = null;
      foreach ($deliveries as $i => $delivery) {
        $deliveryObj = DeliveryTypeTable::getInstance()->findOneByCoreId($delivery['mode_id']);
        $minDeliveryDate = DateTime::createFromFormat('Y-m-d', $delivery['date']);
        $deliveryPeriod = $minDeliveryDate->diff($now)->days;
        if ($deliveryPeriod < 0) $deliveryPeriod = 0;
        $deliveryPeriod = myToolkit::fixDeliveryPeriod($delivery['mode_id'], $deliveryPeriod);
        if ($deliveryPeriod === false) continue;
        $delivery['period'] = $deliveryPeriod;
        $delivery['object'] = $deliveryObj->toArray(false);
        $delivery['text'] = myToolkit::formatDeliveryDate($deliveryPeriod);
        $result['deliveries'][] = $delivery;
        if ($delivery['mode_id'] == 1) {
          $deliveryData = $delivery;
        }
      }
      if ($deliveryData === null) {
        $deliveryData = reset($deliveries);
      }
      $result['delivery'] = $delivery;
      $data[$productId] = $result;
    }
    return $this->renderJson($data);
  }

  public function executeShow(sfWebRequest $request)
  {
    $table = ProductTable::getInstance();

    $field = 'id';
    $id = $request['product'];
    foreach (array('id', 'token', 'core_id', 'barcode', 'article') as $v)
    {
      if (0 === strpos($request['product'], $v)) {
        $field = $v;
        $id = preg_replace('/^' . $v . '/', '', $id);
        break;
      }
    }

    $product = ProductTable::getInstance()->findOneBy($field, $id);
    $this->redirect(array('sf_route' => 'productCard', 'sf_subject' => $product), 301);
  }
}
