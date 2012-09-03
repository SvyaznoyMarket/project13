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

    if (isset($this->productCategory) && !empty($this->productCategory)) {
      $ancestorList = ProductCategoryTable::getInstance()->getAncestorList($this->productCategory, array(
        'hydrate_array' => true,
        'select' => 'productCategory.id, productCategory.token, productCategory.token_prefix, productCategory.name',
      ));
      foreach ($ancestorList as $ancestor)
      {
        $list[] = array(
          'name' => $ancestor['name'],
          'url' => $this->generateUrl('productCatalog_category', array('productCategory' => $ancestor['token_prefix'] ? ($ancestor['token_prefix'] . '/' . $ancestor['token']) : $ancestor['token'])),
        );
      }
      $list[] = array(
        'name' => (string)$this->productCategory,
        'url' => $this->generateUrl('productCatalog_category', $this->productCategory),
      );
    }
    if (isset($this->creator)) {
      $list[] = array(
        'name' => (string)$this->creator,
        'url' => $this->generateUrl('productCatalog_creator', array('sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
    }
    if ($this->productPager) {
      if ($this->productPager->getPage() > 1) {
        $list[] = array(
          'name' => 'страница '.$this->productPager->getPage().' из '.$this->productPager->getLastPage(),
          'url'  => $this->generateUrl('productCatalog_category', $this->productCategory),
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

    if (isset($this->productCategory) && !empty($this->productCategory)) {
      $ancestorList = ProductCategoryTable::getInstance()->getAncestorList($this->productCategory, array(
        'hydrate_array' => true,
        'select' => 'productCategory.id, productCategory.token, productCategory.token_prefix, productCategory.name, productCategory.seo_header',
      ));
      if ($ancestorList) {
        foreach ($ancestorList as $ancestor)
        {
          $list[] = array(
            'name' => $ancestor['seo_header'] ? $ancestor['seo_header'] : $ancestor['name'],
            'url' => $this->generateUrl('productCatalog_category', array('productCategory' => $ancestor['token_prefix'] ? ($ancestor['token_prefix'] . '/' . $ancestor['token']) : $ancestor['token'])),
          );
        }
      }
      $list[] = array(
        'name' => (string)($this->productCategory->seo_header) ? $this->productCategory->seo_header : $this->productCategory->name,
        'url' => $this->generateUrl('productCatalog_category', $this->productCategory),
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
   * @param ProductCategory $productCategory Категория товара
   * @param myDoctrinePager $productPager Листалка товаров
   */
  public function executeArticle_seo()
  {
    // title
    if (empty($this->productCategory->seo_title)) {
      $this->productCategory->seo_title = ''
        . $this->productCategory->name
        . (false == $this->productCategory->isRoot() ? " - {$this->productCategory->getRootCategory()->name}" : '')
        . ( // если передана листалка товаров и номер страницы не равен единице
        ($this->productPager && (1 != $this->productPager->getPage()))
          ? " - Страница {$this->productPager->getPage()} из {$this->productPager->getLastPage()}"
          : ''
        )
        . " - {$this->getUser()->getRegion('name')}"
        . ' - ENTER.ru';
    }
    // description
    if (empty($this->productCategory->seo_description)) {
      $regionName = $this->getUser()->getRegion('name');

      $this->productCategory->seo_description = ''
        . $this->productCategory->name
        . " в {$regionName}"
        . ' с ценами и описанием.'
        . ' Купить в магазине Enter';
    }
    // keywords
    if (empty($this->productCategory->seo_keywords)) {
      $this->productCategory->seo_keywords = "{$this->productCategory->name} магазин продажа доставка {$regionName} enter.ru";
    }

    $this->getResponse()->addMeta('title', $this->productCategory->seo_title);
    $this->getResponse()->addMeta('description', $this->productCategory->seo_description);
    $this->getResponse()->addMeta('keywords', $this->productCategory->seo_keywords);

    if (isset($this->productCategory) && isset($this->productCategory->seo_text)) {
      $this->setVar('article', $this->productCategory->seo_text, true);
    }

  }
}
