<?php

/**
 * creator components.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cart_Components extends myComponents
{
  /**
   * Executes show component
   *
   */
  public function executeShow()
  {
    $this->setVar('list', $this->getProductServiceList($this->getUser()->getCart()), true);
  }

  /**
   * @param UserCartNew $cart
   * @return array
   */
  private function getProductServiceList($cart){

    $urls = sfConfig::get('app_product_photo_url');
    $urlsService = sfConfig::get('app_service_photo_url');

    $prods = $cart->getProducts();
    $services = $cart->getServices();

    $prodIdList = array_keys($prods);
    $serviceIdList = array_keys($services);

    $productList = array();
    $serviceList = array();

    $prodCb = function($data) use(&$productList, $prods, $urls){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $prods[$product->getId()];

        $productList[$product->getId()] = array(
          'type' => 'product',
          'id' => $product->getId(),
          'core_id' => $product->getId(),
          'token_prefix' => $product->getPrefix(),
          'token' => $product->getToken(),
          'link' => $product->getLink(),
          'name' => $product->getNameWeb(),
          'quantity' => $cartInfo->getQuantity(),
          'service' => array(),
          'price' => $product->getPrice(),
          'priceFormatted' =>  number_format($product->getPrice(), 0, ',', ' '),
          'total' => number_format($cartInfo->getTotalPrice(), 0, ',', ' '),
          'photo' => $urls[1] . $product->getMediaImage(),
          'fullObject' => $product,
          'availableForPurchase' => (!$cartInfo->hasError()),
        );
      }
    };

    $serviceCb = function($data) use(&$serviceList, $services, $urlsService){
      /** @var $data ServiceEntity[] */

      foreach($data as $serviceCoreInfo){
        /** @var $cartInfo \light\ServiceCartData[]  array('productId' => ServiceCartData, 'productId' => ServiceCartData)*/

        if(!array_key_exists($serviceCoreInfo->getId(), $services)){
          sfContext::getInstance()->getLogger()->warning('core response contains service (id: "'.$serviceCoreInfo->getId().'") not contained in Cart ("'.json_enocde(array_keys($services)).'")');
          continue;
        }

        $cartInfo = $services[$serviceCoreInfo->getId()];

        if(!array_key_exists($serviceCoreInfo->getId(), $serviceList)){
          $serviceList[$serviceCoreInfo->getId()] = array();
        }

        foreach ($cartInfo as $prodId => $prodServInfo)
        {
          $serviceList[$serviceCoreInfo->getId()][$prodId] = array(
            'type'      => 'service',
            'id'        => $serviceCoreInfo->getId(),
            'core_id'   => $serviceCoreInfo->getId(),
            'token'     => $serviceCoreInfo->getToken(),
            'name'      => $serviceCoreInfo->getName(),
            'quantity'  => $prodServInfo->getQuantity(),
            'service'   => array(
              'id'        => $serviceCoreInfo->getId(),
              'token'     => $serviceCoreInfo->getToken(),
              'quantity'  => $prodServInfo->getQuantity(),
              'price'     => $serviceCoreInfo->getPrice()),
            'price'     => $serviceCoreInfo->getPrice(),
            'total'     => number_format($prodServInfo->getTotalPrice(), 0, ',', ' '),
            'priceFormatted'  => number_format($serviceCoreInfo->getPrice(), 0, ',', ' '),
            'photo' => $urlsService[2] . $serviceCoreInfo->getMediaImage(),
            'availableForPurchase' => (!$prodServInfo->hasError()),
          );
        }
      }
    };
    RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIdList, true);
    CoreClient::getInstance()->execute();

    $list = array_values($productList);

    foreach ($serviceList as $serviceId => $services){
      foreach($services as $productId => $serviceArray){
        if(((int)$productId > 0) && array_key_exists($productId, $productList)){
          //Добавляем услугу к продукту
          foreach($list as $key => $val){
            if($val['id'] == $productId){
              $list[$key]['service'][] = $serviceArray;
              break;
            }
          }
        }
        else{
          // Услуга лежит отдельно
          $list[] = $serviceArray;
        }
      }
    }
    return $list;
  }
}
