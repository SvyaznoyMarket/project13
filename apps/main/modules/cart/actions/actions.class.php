<?php

/**
 * cart actions.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cartActions extends myActions {

    private $_validateResult;

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        $cart = $this->getUser()->getCart();
        $this->setVar('cart', $cart, true);
    }

    private function _refuse() {
        return $this->renderJson(array(
            'success' => $this->_validateResult['success'],
            'data' => array(
                'error' => $this->_validateResult['error'],
            ),
        ));
    }

    /**
     * Executes add action
     *
     * @param sfRequest $request A request object
     */
    public function executeAdd(sfWebRequest $request) {
        $result['value'] = true;
        $result['error'] = "";
        //валидация количества товара
        if (!isset($request['quantity'])) {
            $request['quantity'] = 1;
        }
        elseif ((string)(int)$request['quantity'] !== (string)$request['quantity'])
        {
            $this->_validateResult['success'] = false;
            $this->_validateResult['error'] = "Некорректное количество товара.";
            return $this->_refuse();
        }

        $productId = $request['product'];
        //$product = ProductTable::getInstance()->getByToken($request['product'], array('with_model' => true));
        if (!$productId) {
            $this->_validateResult['success'] = false;
            $this->_validateResult['error'] = "Товар " . $request['product'] . " не найден.";
            return $this->_refuse();
        }

        try
        {
            if (0) { //} $product->isKit()) {
                $request['quantity'] = $this->addKit($product, $request['quantity']);
            }
            else
            {
                $request['quantity'] = $this->addProduct($productId, $request['quantity']);
            }
        }
        catch (Exception $e)
        {
            $result['value'] = false;
            $result['error'] = "Не удалось добавить в корзину товар token='" . $request['product'] . "'.";
            return $this->_refuse();
        }

        $this->getUser()->setCacheCookie();

        if ($request->isXmlHttpRequest()) {
            $cartInfo = $this->getUser()->getCart()->getBaseInfo();
            $return = array(
                'success' => $result['value'],
                'data' => array(
                    'quantity' => $request['quantity'],
                    'full_quantity' => $cartInfo['qty'],
                    'full_price' => $this->getUser()->getCart()->getTotal(),
                    'link' => $this->generateUrl('order_new'),
                    //'html' => $this->getComponent($this->getModuleName(), 'buy_button', array('product' => $product))
                )
            );

            return $this->renderJson($return);
        }
        $this->redirect($this->getRequest()->getReferer());
    }

    /**
     * Executes delete action
     *
     * @param sfRequest $request A request object
     */
    public function executeDelete(sfWebRequest $request) {
        $product = $request['product'];

        if ($product) {
            $this->getUser()->getCart()->deleteProduct($product);
            $this->getUser()->setCacheCookie();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->renderJson(array(
                'success' => true,
            ));
        }
        $this->redirect($this->getRequest()->getReferer());
    }

    /**
     * Executes clear action
     *
     * @param sfRequest $request A request object
     */
    public function executeClear(sfWebRequest $request) {
        $this->getUser()->getCart()->clear();
        $this->getUser()->setCacheCookie();

        $this->redirect($this->getRequest()->getReferer());
    }

    public function executeServiceAdd(sfWebRequest $request) {

        //валидация количества услуг
        if (!isset($request['quantity'])) {
            $request['quantity'] = 1;
        }
        elseif ((string)(int)$request['quantity'] !== (string)$request['quantity'])
        {
            $this->_validateResult['success'] = false;
            $this->_validateResult['error'] = "Некорректное количество услуг.";
            return $this->_refuse();
        }
        $serviceId = $request['service'];

        if (!$serviceId) {
            $this->_validateResult['success'] = false;
            $this->_validateResult['error'] = "Услуга " . $serviceId . " не найдена.";
            return $this->_refuse();
        }

        try
        {
            //если передан продукт, но такой продукт не существует,
            //добавляем услугу без привязки к продукту
            $productId = $request['product'];

            $added = array();
            $currentNum = $this->getUser()->getCart()->getServiceQuantityById($serviceId, $productId);
            $request['quantity'] = $request['quantity'] + $currentNum;


            if ($request['quantity'] <= 0) {
                $request['quantity'] = 0;
                $this->getUser()->getCart()->deleteService($serviceId, $productId);
            }
            else
            {
                $ok = $this->getUser()->getCart()->addService($serviceId, $request['quantity'], $productId);
                if (!$ok) {
                    $this->_validateResult['success'] = false;
                    $this->_validateResult['error'] = "Невозможно добавить услугу к товару, которого нет в корзине.";
                    return $this->_refuse();
                }
            }
            $added[] = array('service' => $serviceId, 'quantity' => $request['quantity']);
        }
        catch (Exception $e)
        {
            $this->_validateResult['success'] = false;
            $this->_validateResult['error'] = "Не удалось добавить в корзину услугу token='" . $request['service'] . "'.";
            return $this->_refuse();
        }

        $this->getUser()->setCacheCookie();

        #myDebug::dump(  $this->getUser()->getCart()->getServices()  );

        if ($request->isXmlHttpRequest()) {
            $cartInfo = $this->getUser()->getCart()->getBaseInfo();
            $return = array(
                'success' => true,
                'data' => array(
                    'quantity' => $request['quantity'],
                    'full_quantity' => $cartInfo['qty'],
                    'full_price' => $this->getUser()->getCart()->getTotal(),
                    'link' => $this->generateUrl('order_new'),
                )
            );

            return $this->renderJson($return);
        }

        $this->redirect($this->getRequest()->getReferer());
    }

    /**
    public function executeServiceAdd(sfWebRequest $request) {
    $product = ProductTable::getInstance()->getByToken($request['product']);
    $service = ServiceTable::getInstance()->findOneByToken($request['service']);

    $this->getUser()->getCart()->addService($product, $service, $request['quantity']);
    $this->getUser()->setCacheCookie();

    $this->redirect($this->getRequest()->getReferer());
    } */
    public function executeServiceDelete(sfWebRequest $request) {
        $serviceId = $request['service'];
        $productId = $request['product'];

        if (!$productId) {
            $productId = 0;
        }
        $this->getUser()->getCart()->deleteService($serviceId, $productId);
        $this->getUser()->setCacheCookie();

        if ($request->isXmlHttpRequest()) {
            $cartInfo = $this->getUser()->getCart()->getBaseInfo();
            $return = array(
                'success' => true,
                'data' => array(
                    'full_quantity' => $cartInfo['qty'],
                    'full_price' => $this->getUser()->getCart()->getTotal(),
                )
            );

            return $this->renderJson($return);
        }

        $this->redirect($this->getRequest()->getReferer());
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return int - количество этого товара в корзине после изменений
     */
    private function addProduct($productId, $quantity) {
        $currentNum = $this->getUser()->getCart()->getQuantityById($productId);
        $quantity += $currentNum;

        if ($quantity <= 0) {
            $quantity = 0;
            $this->getUser()->getCart()->deleteProduct($productId);
        }
        else
        {
            $this->getUser()->getCart()->addProduct($productId, $quantity);
        }

        return $quantity;
    }

    /**
     * @param Product $product
     * @param $quantity
     * @return int - суммарное количество всех товаров из набора в корзине после добавления
     */
    private function addKit(Product $product, $quantity) {
        $i=0;
        $products = ProductTable::getInstance()->getQueryByKit($product)->execute();
        foreach ($products as $subProduct) {
            $productQuantity = $quantity;
            foreach ($subProduct['KitRelation'] as $KitRelation) {
                if ($KitRelation['kit_id'] == $product['id']) {
                    $productQuantity = $quantity * $KitRelation['quantity'];
                    break;
                }
            }
            $totalInCart = $this->addProduct($subProduct, $productQuantity);
            $i+= $totalInCart;
        }
        return $i;
    }

}
