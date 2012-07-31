<?php

/**
 * service components.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceComponents extends myComponents
{
  public function executeList_for_product_in_cart()
    {
      /** @var $product ProductEntity */
      if(array_key_exists('fullObject', $this->product)){
        $product = $this->product['fullObject'];
      }
      else{
        $productList = RepositoryManager::getProduct()->getListById(array($this->product['core_id']), true);
        $product = reset($productList);
      }

      $result = array();
      $selectedNum = 0;
      foreach ($product->getServiceList() as $service)
      {
        $token = $service->getSiteToken();
        if (!$token) {
            continue;
        }
        $sel = false;
        foreach ($this->services as $selected)
        {
          if ($service->getId() == $selected['core_id'])
          {
            $selInfo = $selected;
            $sel = true;
            break;
          }
        }
        if ($sel)
        {
          $selectedNum++;
          $selInfo['selected'] = true;
          $selInfo['site_token'] = $token;
          $selInfo['total'] = $selInfo['quantity'] * $service->getPrice();
          $selInfo['totalFormatted'] = number_format($selInfo['quantity'] * $service->getPrice(), 0, ',', ' ');
          $selInfo['price'] = $service->getPrice();
          $selInfo['only_inshop'] = $service->getOnlyInShop();
          $selInfo['in_sale'] = $service->isInSale();
          $selInfo['priceFormatted'] = number_format($service->getPrice());
          $result[] = $selInfo;
        }
        else
        {
          $result[] = array(
            'selected' => false,
            'site_token' => $token,
            'name' => $service->getName(),
            'id' => $service->getId(),
            'token' => $service->getToken(),
            'price' => $service->getPrice(),
            'only_inshop' => $service->getOnlyInShop(),
            'in_sale' => $service->isInSale(),
            'priceFormatted' => number_format($service->getPrice()),
          );
        }
      }
      $this->setVar('selectedNum', $selectedNum, true);
      $this->setVar('list', $result, true);
    }
}

