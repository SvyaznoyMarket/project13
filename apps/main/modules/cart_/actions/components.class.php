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
    $list = $this->getProductServiceList($this->getUser()->getCart());

    $dataForCredit = array();
    $totalProductPrice =0;
    foreach($list as $product){
      if($product['type'] == 'product'){
        /** @var $obj ProductEntity */
        $obj = $product['fullObject'];
        $rootCategoryToken = '';

        foreach($obj->getCategoryList() as $category){
          if($category->getLevel() == 1){
            $rootCategoryToken = $category->getToken();
            break;
          }
        }

        $dataForCredit[] = array(
          'id' => $product['id'],
          'quantity' => $product['quantity'],
          'price' => $product['price'],
          'type' => CreditBankRepository::getCreditTypeByCategoryToken($rootCategoryToken),
        );
        $totalProductPrice += ($product['price'] * $product['quantity']);
      }
    }

    $this->setVar('list', $list, true);
    $this->setVar('dataForCredit', json_encode($dataForCredit));
    $this->setVar('totalProductPrice', $totalProductPrice);
  }

  /**
   * @param UserCartNew $cart
   * @return array
   */
  private function getProductServiceList($cart){

    $urlsService = sfConfig::get('app_service_photo_url');

    $prods = $cart->getProducts();
    $services = $cart->getServices();

    $prodIdList = array_keys($prods);
    $serviceIdList = array_keys($services);

    $productList = array();
    $serviceList = array();

    $prodCb = function($data) use(&$productList, $prods){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $prods[$product->getId()];

        $rootCategoryToken = '';

        foreach($product->getCategoryList() as $category){
          if($category->getLevel() == 1){
            $rootCategoryToken = $category->getToken();
            break;
          }
        }

        $productList[$product->getId()] = array(
          'type' => 'product',
          'id' => $product->getId(),
          'core_id' => $product->getId(),
          'token_prefix' => $product->getPrefix(),
          'token' => $product->getToken(),
          'link' => $product->getLink(),
          'name' => $product->getName(),
          'quantity' => $cartInfo->getQuantity(),
          'service' => array(),
          'price' => $product->getPrice(),
          'priceFormatted' =>  number_format($product->getPrice(), 0, ',', ' '),
          'total' => number_format($cartInfo->getTotalPrice(), 0, ',', ' '),
          'photo' => $product->getMediaImageUrl(1),
          'fullObject' => $product,
          'availableForPurchase' => (!$cartInfo->hasError()),
          'credit_data_type' =>CreditBankRepository::getCreditTypeByCategoryToken($rootCategoryToken),
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
    if(count($prodIdList)){
      RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    }
    if(count($serviceIdList)){
      RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIdList, true);
    }

    if(count($serviceIdList) || count($prodIdList)){
      CoreClient::getInstance()->execute();
    }

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
          $list[] = array_merge(array('product_id' => (int)$productId), $serviceArray);
        }
      }
    }
    return $list;
  }
}
