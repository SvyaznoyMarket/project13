<?php

class RepositoryManager
{
  /**
   * @static
   * @return RegionRepository
   */
  public static function getRegion()
  {
    static $repo;
    if (!$repo) $repo = new RegionRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductRepository
   */
  public static function getProduct()
  {
    static $repo;
    if (!$repo) $repo = new ProductRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductLabelRepository
   */
  public static function getProductLabel()
  {
    static $repo;
    if (!$repo) $repo = new ProductLabelRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductCategoryFilterRepository
   */
  public static function getProductCategoryFilter()
  {
    static $repo;
    if (!$repo) $repo = new ProductCategoryFilterRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductTypeRepository
   */
  public static function getProductType()
  {
    static $repo;
    if (!$repo) $repo = new ProductTypeRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductCategoryRepository
   */
  public static function getProductCategory()
  {
    static $repo;
    if (!$repo) $repo = new ProductCategoryRepository();
    return $repo;
  }

  /**
   * @static
   * @return ProductCategoryTagViewRepository
   */
  public static function getProductCategoryTagView()
  {
    static $repo;
    if (!$repo) $repo = new ProductCategoryTagViewRepository();
    return $repo;
  }

  /**
   * @static
   * @return ListingRepository
   */
  public static function getListing()
  {
    static $repo;
    if (!$repo) $repo = new ListingRepository();
    return $repo;
  }
}