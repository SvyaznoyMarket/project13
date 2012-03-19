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
   * @return RegionRepository
   */
  public static function getRegion()
  {
    return self::get('Region');
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
   * @return ProductLabelRepository
   */
  public static function getProductLabel()
  {
    return self::get('ProductLabel');
  }
}