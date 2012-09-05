<?php

/**
 * productCatalog_ components.
 *
 * @package    enter
 * @subpackage productCatalog_
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCatalog_Components extends myComponents
{
  /**
   * Executes navigation component
   *
   * @internal-param ProductCategory $productCategory Категория товара
   * @internal-param Creator $creator Бренд
   */
  public function executeNavigation()
  {
    $list = array();

    if ($this->productCategory instanceof ProductCategoryEntity) {
      foreach ($this->productCategory->getAncestors() as $ancestor)
      {
        /** @var $ancestor ProductCategoryEntity */
        $list[] = array(
          'name' => $ancestor->getName(),
          'url'  => $ancestor->getLink(),
        );
      }
      $list[] = array(
        'name' => (string)$this->productCategory,
        'url'  => $this->productCategory->getLink(),
      );
    }
    if ($this->productPager) {
      if ($this->productPager->getPage() > 1) {
        $list[] = array(
          'name' => 'страница '.$this->productPager->getPage().' из '.$this->productPager->getLastPage(),
          'url'  => $this->productCategory->getLink(),
        );
      }
    }
    if (isset($this->product)) {
      if ('productStock' == $this->getContext()->getRouting()->getCurrentRouteName()) {
        $list[] = array(
          'name' => 'Где купить ' . mb_lcfirst((string)$this->product),
          'url' => $this->generateUrl('productCard', array('sf_subject' => $this->product)),
        );
      }
      else {
        $list[] = array(
          'name' => (string)$this->product,
          'url' => $this->generateUrl('productCard', array('sf_subject' => $this->product)),
        );
      }
    }

    $this->setVar('list', $list, false);
  }

  /**
   * Executes navigation component
   *
   * @internal-param ProductCategory $productCategory Категория товара
   * @internal-param Creator $creator Бренд
   */
  public function executeNavigation_seo()
  {
    $list = array();

    if ($this->productCategory instanceof ProductCategoryEntity) {
      foreach ($this->productCategory->getAncestors() as $ancestor)
      {
        /** @var $ancestor ProductCategoryEntity */
        $list[] = array(
          'name' => $ancestor->getSeoHeader() ?: $ancestor->getName(),
          'url'  => $ancestor->getLink(),
        );
      }

      $list[] = array(
        'name' => $this->productCategory->getSeoHeader() ?: $this->productCategory->getName(),
        'url'  => $this->productCategory->getLink(),
      );
    }
    if (isset($this->product)) {
      $list[] = array(
        'name' => (string)$this->product,
        'url' => $this->generateUrl('productCard', array('sf_subject' => $this->product)),
      );
    }

    $this->setVar('list', $list, false);
  }

  /**
   * Executes article_seo component
   *
   * @param ProductCategoryEntity $productCategory Категория товара
   * @param myDoctrinePager $productPager Листалка товаров
   */
  public function executeArticle_seo()
  {
    // title
    if (!$this->productCategory->getSeoTitle()) {
      $this->productCategory->setSeoTitle(''
        . $this->productCategory->getName()
        . ($this->productCategory->getRoot() ? " - {$this->productCategory->getRoot()->getName()}" : '')
        . ( // если передана листалка товаров и номер страницы не равен единице
        ($this->productPager && (1 != $this->productPager->getPage()))
          ? " - Страница {$this->productPager->getPage()} из {$this->productPager->getLastPage()}"
          : ''
        )
        . " - {$this->getUser()->getRegion('name')}"
        . ' - ENTER.ru'
      );
    }
    // description
    if (!$this->productCategory->getSeoDescription()) {
      $regionName = $this->getUser()->getRegion('name');

      $this->productCategory->setSeoDescription(''
        . $this->productCategory->getName()
        . " в {$regionName}"
        . ' с ценами и описанием.'
        . ' Купить в магазине Enter'
      );
    }
    // keywords
    if (!$this->productCategory->getSeoKeywords()) {
      $this->productCategory->setSeoKeywords("{$this->productCategory->getName()} магазин продажа доставка {$regionName} enter.ru");
    }

    $this->getResponse()->addMeta('title', $this->productCategory->getSeoTitle());
    $this->getResponse()->addMeta('description', $this->productCategory->getSeoDescription());
    $this->getResponse()->addMeta('keywords', $this->productCategory->getSeoKeywords());

    if ($this->productCategory instanceof ProductCategoryEntity && $this->productCategory->getSeoText()) {
      $this->setVar('article', $this->productCategory->getSeoText(), true);
    }

  }
}
