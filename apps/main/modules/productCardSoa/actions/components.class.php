<?php

/**
 * productCard components.
 *
 * @package    enter
 * @subpackage productCard
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCardSoaComponents extends myComponents
{
  /**
   * Executes navigation component
   *
   * @param Product $product Товар
   */
  public function executeNavigation()
  {
    $list = array();
    $isSeo = $this->seo;
    if ($isSeo) {
      $this->setVar('tpl_postfix', '_seo');
    } else {
      $this->setVar('tpl_postfix', '');
    }
    if (isset($this->product->category)) {
      foreach ($this->product->category as $c) {
        $link = str_replace('/catalog/', '', $c['link']);
        if (substr($link, -1, 1) == '/') {
          $link = substr($link, 0, -1);
        }
        if ($isSeo && isset($c['seo_header']) && $c['seo_header']) {
          $name = $c['seo_header'];
        } else {
          $name = $c['name'];
        }
        $list[] = array(
          'name' => $name,
          'url' => $this->generateUrl('productCatalog_category', array('productCategory' => $link)),
        );
        //break;
      }
    }

    $list[] = array(
      'name' => $this->product->name,
      'url' => $this->generateUrl('productCard', array('sf_subject' => $this->product)),
    );
    //print_r($list);
    $this->setVar('list', $list, true);
  }

  function executeHeader_meta_og()
  {
    $defaultDescription = 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
    $description = '';
    if ($this->product->description) {
      $description = $this->product->description;
    } elseif ($this->product->tagline) {
      $description = $this->product->tagline;
    } else {
      $description = $defaultDescription;
    }
    $this->setVar('title', $this->product->name);
    $this->setVar('description', $description);
    $this->setVar('photo', $this->product->getMainPhotoUrl(3));
  }
}
