<?php

class RepositoryManager
{
  /**
   * @static
   * @param $name название репозитория
   * @return BaseRepository
   */
  public static function get($name)
  {
    $class = $name.'Repository';

    return new $class();
  }

  /**
   * @static
   * @return ShopRepository
   */
  public static function getShop()
  {
    return self::get('Shop');
  }

  /**
   * @static
   * @return DeliveryTypeRepository
   */
  public static function getDeliveryType()
  {
    return self::get('DeliveryType');
  }

  /**
   * @static
   * @return ProductRepository
   */
  public static function getProduct()
  {
    return self::get('Product');
  }

  /**
   * @static
   * @return ProductTypeRepository
   */
  public static function getProductType()
  {
    return self::get('ProductType');
  }

  /**
   * @static
   * @return ProductCategoryFilter
   */
  public static function getProductCategoryFilter()
  {
    return self::get('ProductCategoryFilter');
  }

  /**
   * @static
   * @return ProductLabelRepository
   */
  public static function getProductLabel()
  {
    return self::get('ProductLabel');
  }
}