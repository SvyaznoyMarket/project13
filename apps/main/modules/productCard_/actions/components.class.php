<?php

/**
 * productCard components.
 *
 * @package    enter
 * @subpackage productCard_
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property $seo
 * @property ProductEntity|Product $product
 */
class productCard_Components extends myComponents
{
  public function executeNavigation()
  {
    $list = array();
    $isSeo = $this->seo;
    if ($isSeo) {
        $this->setVar('tpl_postfix', '_seo');
    } else {
        $this->setVar('tpl_postfix', '');
    }
    $list[] = array(
      'name' => 'Главная',
      'url'  => $this->generateUrl('homepage'),
    );
    $list[] = array(
      'name' => 'Каталог товаров',
      'url'  => $this->generateUrl('productCatalog'),
    );

    // for legacy code
    if($this->product instanceof Product){
      if (isset($this->product->Category))
      {
        foreach ($this->product->Category as $c) {
          $list[] = array(
            'name' => $c->name,
            'url'  => $this->generateUrl('productCatalog_category', $c),
          );
          break;
        }
      }
      $list[] = array(
        'name' => $this->product->name,
        'url'  => $this->generateUrl('productCard', array('sf_subject' => $this->product)),
      );
    // for new code
    } else{
      /** @var $category ProductCategoryEntity */
      foreach ($this->product->getCategoryList() as $category) {
        $list[] = array(
          'name' => $isSeo && $category->getSeoHeader() ? $category->getSeoHeader() : $category->getName(),
          'url'  => $category->getLink(),
        );
      }

      $list[] = array(
        'name' => $this->product->getName(),
        'url'  => $this->product->getLink(),
      );
    }

    if (true === $this->isComment) {
      $list[] = array(
        'name' => $this->product->getName().' - отзывы',
        'url'  => ($this->product instanceof Product) ? $this->generateUrl('productComment_new', array('sf_subject' => $this->product)) : $this->product->getLink().'/comments',
      );
    }

    $this->setVar('list', $list, true);
  }

  function executeHeader_meta_og()
  {
    if ($this->product->getDescription()) {
        $description = $this->product->getDescription();
    } elseif ($this->product->getTagline()) {
        $description = $this->product->getTagline();
    } else {
        $description = 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
    }
    $this->setVar('title', $this->product->getName());
    $this->setVar('description', $description);
    $this->setVar('photo', $this->product->getMediaImageUrl(3));
  }
}
