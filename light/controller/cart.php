<?php
namespace light;
use Logger;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 20.06.12
 * Time: 16:45
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

class cartController
{

  public function setProductQuantity(Response $response, $params = array()){
    TimeDebug::start('controller:cart:addProduct');

    $logger = \Logger::getLogger('Cart');

    \LoggerNDC::push('addProduct');

    $result['value'] = true;
    $result['error'] = "";

    try{
      $quantity = intval(array_key_exists('quantity', $_GET) ? $_GET['quantity'] : 1);
      if($quantity < 1){
        throw new \Exception('Указано неверное количество товаров');
      }

      if(!array_key_exists('productId', $_GET)){
        $logger->error('Product not specified');
        throw new \Exception("Не указано, какой товар необходимо добавить в корзину");
      }

      $productId = (int) $_GET['productId'];

      if (!$productId) {
        $logger->error('Product with id "' . $productId . '" not found');
        throw new \InvalidArgumentException("Товар с Id" . $productId . " не найден.");
      }

      $productList = App::getProduct()->getProductsByIdList(array($productId));
      if(count($productList) < 1){
        $logger->error('Product with id "' . $productId . ' not found on core side');
        throw new \Exception("Товар с Id" . $productId . " не найден на стороне ядра.");
      }

      $product = $productList[0];
      $productList = null;

      if ($product->isKit()) {
        $quantity = $result['quantity'] = $this->executeAddKit($product, $quantity);
      }
      elseif($_GET['quantity'] == Null)
      {
          $quantity = $result['quantity'] = $this->executeAddProduct($productId);
      }
      else
      {
          $quantity = $result['quantity'] = $this->executeSetProductQuantity($productId, $quantity);
      }

      // обновить количество гарантий для товара
      foreach (App::getCurrentUser()->getCart()->getWarrantyList() as $warrantyId => $warrantiesByProduct) {
        /** @var $warranty WarrantyCartData */
        foreach ($warrantiesByProduct as $id => $warranty) {
          if ($id == $product->getId()) {
            App::getCurrentUser()->getCart()->setWarranty($warrantyId, $product->getId(), $quantity);
          }
        }
      }

      if(App::getRequest()->isXmlHttpRequest()){
        $return = array(
          'success' => true,
          'data' => array(
            'quantity' => $quantity,
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($return));
        $response->setContentType('application/json');
        TimeDebug::end('controller:cart:addProduct');
      }
      else{
        TimeDebug::end('controller:cart:addProduct');
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }
    }
    catch(\Exception $e){
      $return = array(
        'success' => false,
        'data' => array(
          'error' => "Не удалось добавить товар в корзину",
          'debug' => $e->getMessage()
        ),
      );
      $response->setContentType('application/json');
      $response->setContent(json_encode($return));
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:addProduct');
      return;
    }
    \LoggerNDC::pop();
  }

  public function setWarranty(Response $response, $params = array()){
    TimeDebug::start('controller:cart:setWarranty');
    $logger = \Logger::getLogger('Cart');
    \LoggerNDC::push('setWarranty');

    try{
      if(!array_key_exists('warrantyId', $_GET)){
        $logger->error('Warranty not specified');
        throw new \InvalidArgumentException('Не указано, какую гарантию необходимо добавить в корзину');
      }
      $warrantyId = (int)$_GET['warrantyId'];

      if(!$warrantyId){
        $logger->error('Warranty with id "' . $warrantyId . '" not found');
        throw new \InvalidArgumentException('Гарантия с Id '. $warrantyId . " не найдена.");
      }

      $quantity  = (array_key_exists('quantity', $_GET))? $_GET['quantity'] : 1;
      $productId = (array_key_exists('productId', $_GET))? (int)$_GET['productId'] : Null;

      if($productId){
        //Если продукта нет - добавляем его
        $productList = App::getProduct()->getProductsByIdList(array($productId));
        if(count($productList) < 1){
          $logger->error('Product with id "' . $productId . '" not found');
          throw new \Exception("невозможно привязать гарантию к несуществующему товару.");
        }

        $product = $productList[0];
        $productList = null;

         // Если товара нет в корзине, то добавить
        if (!App::getCurrentUser()->getCart()->containsProduct($productId)) {
          $this->executeSetProductQuantity($productId, 1);
        }
        $product = App::getCurrentUser()->getCart()->getProduct($productId);

        App::getCurrentUser()->getCart()->setWarranty($warrantyId, $productId, $product ? $product->getQuantity() : $quantity);
      }
      else{
      }

      if(App::getRequest()->isXmlHttpRequest()){
        $return = array(
          'success' => true,
          'data' => array(
            'quantity' => $quantity,
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($return));
        $response->setContentType('application/json');
        TimeDebug::end('controller:cart:setWarranty');
      }
      else{
        TimeDebug::end('controller:cart:setWarranty');
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }

    }
    catch(\Exception $e){
      $return = array(
        'success' => false,
        'data' => array(
          'error' => "Не удалось добавить гарантию в корзину",
          'debug' => $e->getMessage()
        ),
      );
      $response->setContentType('application/json');
      $response->setContent(json_encode($return));
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:setWarranty');
      return;
    }
    \LoggerNDC::pop();
  }

  public function addService(Response $response, $params = array()){
    TimeDebug::start('controller:cart:addService');
    $logger = \Logger::getLogger('Cart');
    \LoggerNDC::push('addService');

    try{
      if(!array_key_exists('serviceId', $_GET)){
        $logger->error('Service not specified');
        throw new \InvalidArgumentException('Не указано, какую услугу необходимо добавить в корзину');
      }
      $serviceId = (int)$_GET['serviceId'];

      if(!$serviceId){
        $logger->error('Service with id "' . $serviceId . '" not found');
        throw new \InvalidArgumentException('Услуга с Id '. $serviceId . " не найдена.");
      }

      $quantity  = (array_key_exists('quantity', $_GET))? $_GET['quantity'] : 1;

      $productId = (array_key_exists('productId', $_GET))? (int)$_GET['productId'] : Null;

      if($productId){
        //Если продукта нет - добавляем его
        $productList = App::getProduct()->getProductsByIdList(array($productId));
        if(count($productList) < 1){
          $logger->error('Product with id "' . $productId . '" not found');
          throw new \Exception("невозможно привязать услугу к несуществующему товару.");
        }

        $product = $productList[0];
        $productList = null;

        // Если товар является набором ...
        if ($product->isKit()) {
          $kitList = $product->getKitList();
          foreach($kitList as $kit) {
            // Если товар набора уже в корзине, то пропустить
            if (App::getCurrentUser()->getCart()->containsProduct($kit->getProductId())) continue;
            $this->executeSetProductQuantity($kit->getProductId(), ($kit->getQuantity() * 1));
          }
        }
        // ... иначе ...
        else {
          // Если товара нет в корзине, то добавить
          if (!App::getCurrentUser()->getCart()->containsProduct($productId)) {
            $this->executeSetProductQuantity($productId, 1);
          }
        }

//        App::getCurrentUser()->getCart()->removeService($serviceId, null, $productId);
        App::getCurrentUser()->getCart()->setServiceQuantity($serviceId, $quantity, $productId);
      }
      else{
//        App::getCurrentUser()->getCart()->removeService($serviceId, null, 0);
        App::getCurrentUser()->getCart()->setServiceQuantity($serviceId, $quantity);
      }

      if(App::getRequest()->isXmlHttpRequest()){
        $return = array(
          'success' => true,
          'data' => array(
            'quantity' => $quantity,
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($return));
        $response->setContentType('application/json');
        TimeDebug::end('controller:cart:addService');
      }
      else{
        TimeDebug::end('controller:cart:addService');
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }

    }
    catch(\Exception $e){
      $return = array(
        'success' => false,
        'data' => array(
          'error' => "Не удалось добавить услугу в корзину",
          'debug' => $e->getMessage()
        ),
      );
      $response->setContentType('application/json');
      $response->setContent(json_encode($return));
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:addService');
      return;
    }
    \LoggerNDC::pop();
  }

  public function deleteProduct(Response $response, $params = array()){
    TimeDebug::start('controller:cart:deleteProduct');
    $logger = \Logger::getLogger('Cart');
    \LoggerNDC::push('deleteProduct');
    try{
      if(!array_key_exists('productId', $_GET)){
        $logger->error('Product not specified');
        throw new \Exception("Не указано, какой товар необходимо удалить из корзины");
      }

      $productId = (int) $_GET['productId'];

      if (!$productId) {
        $logger->error('Product with id "' . $productId . '" not found');
        throw new \InvalidArgumentException("Товар с Id" . $productId . " не найден.");
      }

      App::getCurrentUser()->getCart()->removeProductServices($productId);
      App::getCurrentUser()->getCart()->removeProduct($productId);

      TimeDebug::end('controller:cart:deleteProduct');

      if(App::getRequest()->isXmlHttpRequest()){

        $data = array(
          'success' => true,
          'data' => array(
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($data));
        $response->setContentType('application/json');
        TimeDebug::end('controller:cart:add');
      }
      else{
        TimeDebug::end('controller:cart:add');
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }
    }
    catch(\Exception $e){
      $response->setContent(json_encode(array('success' => false, 'debug' => $e->getMessage())));
      $response->setContentType('application/json');
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:deleteProduct');
    }
    \LoggerNDC::pop();
  }

  public function deleteService(Response $response, $params = array()){
    TimeDebug::start('controller:cart:deleteService');
    $logger = \Logger::getLogger('Cart');
    \LoggerNDC::push('deleteService');
    try{
      if(!array_key_exists('serviceId', $_GET)){
        $logger->error('Service not specified');
        throw new \InvalidArgumentException('Не указано, какую услугу необходимо добавить в корзину');
      }
      $serviceId = (int)$_GET['serviceId'];

      if(!$serviceId){
        $logger->error('Service with id "' . $serviceId . '" not found');
        throw new \InvalidArgumentException('Услуга с Id '. $serviceId . " не найдена.");
      }

      $productId = (array_key_exists('productId', $_GET))? (int)$_GET['productId'] : 1;

      App::getCurrentUser()->getCart()->removeService($serviceId, null, $productId);

      TimeDebug::end('controller:cart:deleteService');

      if(App::getRequest()->isXmlHttpRequest()){

        $data = array(
          'success' => true,
          'data' => array(
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($data));
        $response->setContentType('application/json');
      }
      else{
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }
    }
    catch(\Exception $e){
      $response->setContent(json_encode(array('success' => false, 'debug' => $e->getMessage())));
      $response->setContentType('application/json');
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:deleteService');
    }
    \LoggerNDC::pop();
  }

  public function deleteWarranty(Response $response, $params = array()){
    TimeDebug::start('controller:cart:deleteWarranty');
    $logger = \Logger::getLogger('Cart');
    \LoggerNDC::push('deleteWarranty');
    try{
      if(!array_key_exists('warrantyId', $_GET)){
        $logger->error('Warranty not specified');
        throw new \InvalidArgumentException('Не указано, какую гарантию необходимо добавить в корзину');
      }
      $warrantyId = (int)$_GET['warrantyId'];

      if(!$warrantyId){
        $logger->error('Warranty with id "' . $warrantyId . '" not found');
        throw new \InvalidArgumentException('Гарантия с Id '. $warrantyId . " не найдена.");
      }

      $productId = (array_key_exists('productId', $_GET))? (int)$_GET['productId'] : 1;

      App::getCurrentUser()->getCart()->removeWarranty($warrantyId, $productId);

      TimeDebug::end('controller:cart:deleteWarranty');

      if(App::getRequest()->isXmlHttpRequest()){

        $data = array(
          'success' => true,
          'data' => array(
            'full_quantity' => $this->getTotalQuantityForShow(),
            'full_price' => App::getCurrentUser()->getCart()->getTotalPrice(),
            'link' => App::getRouter()->createUrl('order.new')
          )
        );

        $response->setContent(json_encode($data));
        $response->setContentType('application/json');
      }
      else{
        $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
      }
    }
    catch(\Exception $e){
      $response->setContent(json_encode(array('success' => false, 'debug' => $e->getMessage())));
      $response->setContentType('application/json');
      $logger->error('Error: ' . $e->getMessage());
      TimeDebug::end('controller:cart:deleteWarranty');
    }
    \LoggerNDC::pop();
  }

  public function clear(Response $response, $params = array()){
    App::getCurrentUser()->getCart()->clear();

    $response->redirect((strlen(App::getRequest()->getReferer()) > 0)? App::getRequest()->getReferer() : '/');
  }


  /**
   * @param int $productId
   * @param int $quantity
   * @return int
   */
  private function executeSetProductQuantity($productId, $quantity){
    App::getCurrentUser()->getCart()->setProductQuantity($productId, $quantity);
    return $quantity;
  }

  private function executeAddProduct($productId){
    App::getCurrentUser()->getCart()->addProduct($productId);
  }

  /**
   * @param ProductData $product
   * @param int $quantity
   * @return int
   */
  private function executeAddKit($product, $quantity){
    $kitList = $product->getKitList();
    $sum = 0;
    foreach($kitList as $kit){
      $sum += $this->executeSetProductQuantity($kit->getProductId(), ($kit->getQuantity() * $quantity));
    }
    return $sum;
  }

  /**
   * @return int
   */
  private function getTotalQuantityForShow(){
    return intval(App::getCurrentUser()->getCart()->getProductsQuantity() + App::getCurrentUser()->getCart()->getServicesQuantity(0));
  }

}
