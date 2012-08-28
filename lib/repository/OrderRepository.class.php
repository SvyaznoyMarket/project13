<?php

class OrderRepository
{
  /* @var CoreClient */
  private $coreClient = null;

  public function __construct()
  {
    $this->coreClient = CoreClient::getInstance();
  }

  public function countByUserToken($token) {
    $return = 0;

    $result = $this->coreClient->query('order/get', array('token' => $token));
    if ($result) {
      $return = count($result);
    }

    return $return;
  }

  public function getByUserToken($token) {
    $return = array();

    $result = $this->coreClient->query('order/get', array('token' => $token));
    if ($result) {
      // сортировка заказов в обратном порядке
      $result = array_reverse($result);

      $productIds = array();
      $serviceIds = array();

      foreach ($result as $item) {
        $entity = new OrderEntity($item);

        foreach ($entity->getItem() as $orderItem) {
          if (OrderItemEntity::TYPE_PRODUCT == $orderItem->getType()) {
            $productIds[] = $orderItem->getProduct()->getId();
          }
          else if (OrderItemEntity::TYPE_SERVICE == $orderItem->getType()) {
            $serviceIds[] = $orderItem->getService()->getId();
          }
        }

        $return[] = $entity;
      }

      $productsById = array();
      foreach (RepositoryManager::getProduct()->getListById($productIds) as $product) {
        $productsById[$product->getId()] = $product;
      }

      $servicesById = array();
      foreach (RepositoryManager::getService()->getListById($serviceIds) as $service) {
        $servicesById[$service->getId()] = $service;
      }

      foreach ($return as $order) {
        foreach ($order->getItem() as $orderItem) {
          if (OrderItemEntity::TYPE_PRODUCT == $orderItem->getType()) {
            if (array_key_exists($orderItem->getProduct()->getId(), $productsById)) {
              $orderItem->setProduct($productsById[$orderItem->getProduct()->getId()]);
            }
          }
          else if (OrderItemEntity::TYPE_SERVICE == $orderItem->getType()) {
            if (array_key_exists($orderItem->getService()->getId(), $servicesById)) {
              $orderItem->setService($servicesById[$orderItem->getService()->getId()]);
            }
          }
        }
      }
    }

    return $return;
  }
}