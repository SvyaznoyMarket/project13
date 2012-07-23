<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 28.06.12
 * Time: 18:59
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');

class userController
{
  public function getShortInfo(Response $response, $params = array()){
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
      $response->setStatusCode(404);
      $response->setContent(App::getRenderer()->renderFile('404'));
      return;
    }

    TimeDebug::start('controller:user:getShortInfo');
    $response->setContentType('application/json');

    try{
      $cart = App::getCurrentUser()->getCart();

      $prods = $cart->getProductList();
      $services = $cart->getServiceList();
      $prodIdList = array();
      $serviceIdList = array();
      $serviceNotRelatedQuantity = 0;

      foreach($prods as $prod){
        if(!$prod->hasError()){
          $prodIdList[] = $prod->getProductId();
        }
      }

      foreach($services as $serviceId => $service){
        foreach ($service as $productId=> $tmp){
          if(!$tmp->hasError()){
            $prodIdList[] = $productId;
            $serviceIdList[] = $serviceId;
            if((int)$productId == 0){
              $serviceNotRelatedQuantity++;
            }
          }
        }
      }

      $prodIdList = array_unique($prodIdList);
      $serviceIdList = array_unique($serviceIdList);

      $productInfoList = array();
      $serviceInfoList = array();

      $prodCb = function($data) use(&$productInfoList){
        /** @var $data ProductShortData[] */

        foreach($data as $product){
          $productInfoList[$product->getId()] = $product->getToken();
        }
      };

      $serviceCb = function($data) use(&$serviceInfoList){
        /** @var $data ServiceData[] */

        foreach($data as $service){
          $serviceInfoList[$service->getId()] = $service->getToken();
        }
      };

      if(!count($prodIdList) && !count($serviceIdList)){
        $responseData = array(
          'success' => true,
          'data' => array(
            'name' => App::getCurrentUser()->isAuthorized()? App::getCurrentUser()->getUser()->getFullName() : '',
            'link' => '/private/', //ссылка на личный кабинет
            'vitems' => 0,
            'sum' => 0,
            'vwish' => 0,
            'vcomp' => 0,
            'productsInCart' => array(),
            'servicesInCart' => array(),
            'bingo' => false,
            'region_id' =>App::getCurrentUser()->getRegion()->getId()
          )
        );
        $response->setContent(json_encode($responseData));
        TimeDebug::end('controller:user:getShortInfo');
        return;
      }

      if(count($prodIdList)){
        App::getProduct()->getProductsShortDataByIdListAsync($prodIdList, $prodCb);
      }

      if(count($serviceIdList)){
        App::getService()->getServicesByIdListAsync($serviceIdList, $serviceCb);
      }

      App::getCoreV2()->execute();


      $responseData = array(
        'success' => true,
        'data' => array(
          'name' => App::getCurrentUser()->isAuthorized()? App::getCurrentUser()->getUser()->getFullName() : '',
          'link' => '/private/', //ссылка на личный кабинет
          'vitems' => ($cart->getProductsQuantity() + $serviceNotRelatedQuantity),
          'sum' => $cart->getTotalPrice(),
          'vwish' => 0,
          'vcomp' => 0,
          'productsInCart' => array(),
          'servicesInCart' => array(),
          'bingo' => false,
          'region_id' =>App::getCurrentUser()->getRegion()->getId()
        )
      );

      foreach($prods as $prodId => $prod){
        if(!array_key_exists($prodId, $productInfoList)){
          //@TODO log
          continue;
        }
        $token = $productInfoList[$prodId];
        $responseData['data']['productsInCart'][$token] = $prod->getQuantity();
      }

      foreach ($services as $serviceId => $service){
        if(!array_key_exists($serviceId, $serviceInfoList)){
          //@TODO log
          continue;
        }
        $serviceToken = $serviceInfoList[$serviceId];
        $responseData['data']['servicesInCart'][$serviceToken] = array();

        foreach ($service as $productId => $serviceElem){
          /** @var $serviceElem ServiceCartData */
          if ($productId == 0) {
            $responseData['data']['servicesInCart'][$serviceToken]["0"] = $serviceElem->getQuantity();
            continue;
          }
          if(!array_key_exists($productId, $productInfoList)){
            //@TODO log
            continue;
          }
          $productToken = $productInfoList[$productId];
          $responseData['data']['servicesInCart'][$serviceToken][$productToken] = $serviceElem->getQuantity();
        }
      }
    }
    catch(\Exception $e){
      $responseData = array(
        'success' => false,
        'data' => array(),
        'debug' => $e->getMessage()
      );
    }
    $response->setContent(json_encode($responseData));
    TimeDebug::end('controller:user:getShortInfo');
  }

}
