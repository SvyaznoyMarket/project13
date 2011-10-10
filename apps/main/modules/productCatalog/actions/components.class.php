<?php

/**
 * productCatalog components.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCatalogComponents extends myComponents
{
/**
  * Executes navigation component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param Creator $creator Производитель
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => 'Главная',
      'url'  => url_for('@homepage'),
    );
    $list[] = array(
      'name' => 'Каталог товаров',
      'url'  => url_for('@productCatalog'),
    );
    if (isset($this->productCategory))
    {
      $list[] = array(
        'name' => $this->productCategory->name,
        'url'  => url_for('productCatalog_category', $this->productCategory),
      );
    }
    if (isset($this->creator))
    {
      $list[] = array(
        'name' => $this->creator->name,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes category_list component
  *
  * @param Doctrine_Collection $productCategoryList Коллекция категорий товаров
  */
  public function executeCategory_list()
  {
    $list = array();
    foreach ($this->productCategoryList as $productCategory)
    {
      $list[] = array(
        'name'            => (string)$productCategory,
        'productCategory' => $productCategory,
        'level'           => $productCategory->level,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes categoryChild_list component
  *
  * @param ProductCategory $productCategory Категория товаров
  */
  public function executeCategoryChild_list()
  {
    $list = array();
    foreach ($this->productCategory->getNode()->getChildren() as $productCategory)
    {
      $list[] = array(
        'name'          => (string)$productCategory,
        'url'           => url_for('productCatalog_category', $productCategory),
        'level'         => $productCategory->level,
        'product_count' => $productCategory->getProductCount(),
      );
    }

    $this->setVar('columnList', myToolkit::groupByColumn($list, 4), true);
  }
/**
  * Executes creator_list component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeCreator_list()
  {
    $creatorList = CreatorTable::getInstance()->getListByProductCategory($this->productCategory, array(
      'order' => 'creator.name',
    ));

    $list = array();
    foreach ($creatorList as $creator)
    {
      $list[] = array(
        'name' => (string)$creator,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $creator)),
      );
    }

    $this->setVar('list', $list, true);
  }
/**
  * Executes filter component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param Creator $creator Производитель
  * @param myProductFormFilter $form Форма фильтра с параметрами товаров
  */
  public function executeFilter()
  {
    if (empty($this->form))
    {
      $this->form = new myProductFormFilter(array(), array(
        'productCategory' => $this->productCategory,
        'creator'         => $this->creator,
      ));
    }
    
    $this->url = url_for('productCatalog_filter', $this->productCategory);
  }
/**
  * Executes filter_price component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_price()
  {

  }
 /**
  * Executes filter_creator component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_creator()
  {
  }
  /**
  * Executes filter_parameter component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_parameter()
  {
  }
}
