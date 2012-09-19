<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 28.06.12
 * Time: 18:59
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH . 'system/App.php');
require_once(ROOT_PATH . 'lib/TimeDebug.php');

class userController
{
  public function getShortInfo(Response $response, $params = array())
  {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
      $response->setStatusCode(404);
      $response->setContent(App::getRenderer()->renderFile('404'));
      return;
    }

    TimeDebug::start('controller:user:getShortInfo');
    $response->setContentType('application/json');

    try {
      $cart = App::getCurrentUser()->getCart();

      $products = $cart->getProductList();
      $services = $cart->getServiceList();
      $warranties = $cart->getWarrantyList();
      $productIdList = array();
      $serviceIdList = array();
      $warrantyIdList = array();
      $serviceNotRelatedQuantity = 0;

      foreach ($products as $product) {
        if (!$product->hasError()) {
          $productIdList[] = $product->getProductId();
        }
      }

      foreach ($services as $serviceId => $service) {
        foreach ($service as $productId=> $tmp) {
          if (!$tmp->hasError()) {
            $productIdList[] = $productId;
            $serviceIdList[] = $serviceId;
            if ((int)$productId == 0) {
              $serviceNotRelatedQuantity++;
            }
          }
        }
      }

      $productIdList = array_unique($productIdList);
      $serviceIdList = array_unique($serviceIdList);

      $productInfoList = array();
      $serviceInfoList = array();

      $productCallback = function ($data) use (&$productInfoList) {
        /** @var $data ProductShortData[] */

        foreach ($data as $product) {
          $productInfoList[$product->getId()] = $product->getToken();
        }
      };

      $serviceCallback = function ($data) use (&$serviceInfoList) {
        /** @var $data ServiceData[] */

        foreach ($data as $service) {
          $serviceInfoList[$service->getId()] = $service->getToken();
        }
      };

      if (!count($productIdList) && !count($serviceIdList)) {
        $responseData = array(
          'success' => true,
          'data'    => array(
            'name'             => App::getCurrentUser()->isAuthorized() ? App::getCurrentUser()->getUser()->getFullName() : '',
            'link'             => '/private/', //ссылка на личный кабинет
            'vitems'           => 0,
            'sum'              => 0,
            'vwish'            => 0,
            'vcomp'            => 0,
            'productsInCart'   => array(),
            'servicesInCart'   => array(),
            'warrantiesInCart' => array(),
            'bingo'            => false,
            'region_id'        => App::getCurrentUser()->getRegion()->getId()
          )
        );
        $response->setContent(json_encode($responseData));
        TimeDebug::end('controller:user:getShortInfo');
        return;
      }

      if (count($productIdList)) {
        App::getProduct()->getProductsShortDataByIdListAsync($productIdList, $productCallback);
      }

      if (count($serviceIdList)) {
        App::getService()->getServicesByIdListAsync($serviceIdList, $serviceCallback);
      }

      App::getCoreV2()->execute();

      $responseData = array(
        'success' => true,
        'data'    => array(
          'name'             => App::getCurrentUser()->isAuthorized() ? App::getCurrentUser()->getUser()->getFullName() : '',
          'link'             => '/private/', //ссылка на личный кабинет
          'vitems'           => ($cart->getProductsQuantity() + $serviceNotRelatedQuantity),
          'sum'              => $cart->getTotalPrice(),
          'vwish'            => 0,
          'vcomp'            => 0,
          'productsInCart'   => array(),
          'servicesInCart'   => array(),
          'warrantiesInCart' => array(),
          'bingo'            => false,
          'region_id'        => App::getCurrentUser()->getRegion()->getId()
        )
      );

      foreach ($products as $prodId => $product) {
        if (!array_key_exists($prodId, $productInfoList)) {
          //@TODO log
          continue;
        }
        $token = $productInfoList[$prodId];
        $responseData['data']['productsInCart'][$token] = $product->getQuantity();
      }

      foreach ($services as $serviceId => $service) {
        if (!array_key_exists($serviceId, $serviceInfoList)) {
          //@TODO log
          continue;
        }
        $serviceToken = $serviceInfoList[$serviceId];
        $responseData['data']['servicesInCart'][$serviceToken] = array();

        foreach ($service as $productId => $serviceElem) {
          /** @var $serviceElem ServiceCartData */
          if ($productId == 0) {
            $responseData['data']['servicesInCart'][$serviceToken]["0"] = $serviceElem->getQuantity();
            continue;
          }
          if (!array_key_exists($productId, $productInfoList)) {
            //@TODO log
            continue;
          }
          $productToken = $productInfoList[$productId];
          $responseData['data']['servicesInCart'][$serviceToken][$productToken] = $serviceElem->getQuantity();
        }
      }

      foreach ($warranties as $warrantyId => $warrantiesByProduct) {
        /** @var $warranty WarrantyCartData */
        foreach ($warrantiesByProduct as $productId => $warranty) {
          $productToken = $productInfoList[$productId];
          $responseData['data']['warrantiesInCart'][$warrantyId][$productToken] = $warranty->getQuantity();
        }
      }

    } catch (\Exception $e) {
      $responseData = array(
        'success' => false,
        'data'    => array(),
        'debug'   => $e->getMessage(),
      );
    }
    $response->setContent(json_encode($responseData));
    TimeDebug::end('controller:user:getShortInfo');
  }

}
