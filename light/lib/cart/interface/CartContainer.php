<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 07.06.12
 * Time: 14:16
 * To change this template use File | Settings | File Templates.
 */
interface CartContainer
{

  /**
   * @abstract
   * @param int $productId
   * @param int $quantity
   * @return void
   */
  public function setProductQuantity($productId, $quantity);

    /**
     * @abstract
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function addProduct($productId);

  /**
   * Удаляет первый товар из корзины
   *
   * @abstract
   */
  public function shiftProduct();

  /**
   * @abstract
   * @param int $productId
   * @return bool
   */
  public function hasProduct($productId);

  /**
   * @abstract
   * @param int $productId
   * @param int|null $quantity если null - удалит все
   * @return void
   */
  public function removeProduct($productId, $quantity=null);

  /**
   * @abstract
   * @param int $serviceId
   * @param int $quantity
   * @param int|null $productId если не null - добавит услугу, прилагаемую к указанному товару
   * @return void
   */
  public function addService($serviceId, $quantity, $productId=null);

  /**
   * @abstract
   * @param int $serviceId
   * @param int|null $quantity если null - удалит все
   * @param int $productId если 0 - удалит услугу, не привязанную к продукту
   * @return void
   */
  public function removeService($serviceId, $quantity=null, $productId=null);

  /**
   * @abstract
   * @return array :
   *
   * <code>
   * array(
   *  'productList' => array(
   *    productId => quantity
   *  ),
   *  'serviceList' => array(
   *    serviceId => array( //если 0 - значит нет привязки к товару
   *      productId = quantity
   *    )
   *  )
   * );
   * </code>
   *
   */
//  public function getData();

  /**
   * @abstract
   * @return void
   */
  public function clear();

  /**
   * Ф-я возвращает количество уникальных товаров в корзине
   * @abstract
   * @return int
   */
  public function getProductsQuantity();

  /**
   * Ф-я возвращает количество товара с указанным id в корзние (если его нет - вернет 0)
   * @abstract
   * @param int $productId
   * @return int
   */
  public function getProductQuantity($productId);

  /**
   * Ф-я возвращает количество услуг с указанным id в корзние
   * @abstract
   * @param int $serviceId
   * @param int|null $productId если null - считает сумму всех услуг, и связанных с продуктами и нет
   * @return mixed
   */
  public function getServiceQuantity($serviceId, $productId=null);

  /**
   * Ф-я возвращает количество уникальных умлуг в корзине
   * @abstract
   * @param int|null $productId //Если null - считается во всех услугах, иначе - только для определенного продукта
   * @return int
   */
  public function getServicesQuantity($productId=null);

  /**
   * @abstract
   * @return int[] values - product IDs
   */
  public function getProductIdList();

  /**
   * @abstract
   * @return array :
   *
   * array(
   *  array(
   *    'id' => 1,
   *    'quantity' => 1
   *  ),
   *  array(
   *    'id' => 2,
   *    'quantity' => 1
   *  )
   * )
   *
   */
  public function getProductsQuantities();

  /**
   * @abstract
   * @return array :
   *
   * array(
   *  array(
   *    'id' => 1,
   *    'product_id' => 1,
   *    'quantity' => 1
   *  ),
   *  array(
   *    'id' => 2,
   *    'quantity' => 1
   *  )
   * )
   *
   */
  public function getServicesQuantities();

  /**
   * @abstract
   * @param int|null $productId
   * @return int[]
   */
  public function getServiceIdList($productId=null);

  /**
   * Функция возвращает сумму quantity всех услуг и товаров
   * @abstract
   * @return int
   */
  public function getTotalQuantity();

}
