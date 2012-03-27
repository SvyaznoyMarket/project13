<?php

/**
 * productCatalog_ components.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property ProductCategory $productCategory
 * @property Creator $creator
 * @property Product $product
 * @property ProductCategory[] $productCategoryList
 * @property myProductFormFilter[] $form
 * @property ProductCoreFormFilterSimple $productFilter
 */
class productCatalog_Components extends myComponents
{
  public function executeNavigation()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
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
          'url' => $this->generateUrl('productCatalog__category', array('productCategory' => $ancestor['token_prefix'] ? ($ancestor['token_prefix'] . '/' . $ancestor['token']) : $ancestor['token'])),
        );
      }
      $list[] = array(
        'name' => (string)$this->productCategory,
        'url' => $this->generateUrl('productCatalog__category', $this->productCategory),
      );
    }
    if (isset($this->creator)) {
      $list[] = array(
        'name' => (string)$this->creator,
        'url' => $this->generateUrl('productCatalog__creator', array('sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
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

  public function executeNavigation_seo()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
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
            'url' => $this->generateUrl('productCatalog__category', array('productCategory' => $ancestor['token_prefix'] ? ($ancestor['token_prefix'] . '/' . $ancestor['token']) : $ancestor['token'])),
          );
        }
      }
      $list[] = array(
        'name' => (string)($this->productCategory->seo_header) ? $this->productCategory->seo_header : $this->productCategory->name,
        'url' => $this->generateUrl('productCatalog__category', $this->productCategory),
      );
    }
    if (isset($this->creator)) {
      $list[] = array(
        'name' => (string)$this->creator,
        'url' => $this->generateUrl('productCatalog__creator', array('sf_subject' => $this->productCategory, 'creator' => $this->creator)),
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

  public function executeCategory_list()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
    $list = array();
    foreach ($this->productCategoryList as $productCategory)
    {
      $list[] = array(
        'name' => $productCategory['name'],
        'url' => $this->generateUrl('productCatalog__category', array('productCategory' => $productCategory['token_prefix'] ? ($productCategory['token_prefix'] . '/' . $productCategory['token']) : $productCategory['token'])),
        'level' => $productCategory['level'],
      );
    }

    $this->setVar('list', $list, true);
  }

  public function executeArticle_seo()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
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
      $region = $this->getUser()->getRegion('region');
      $regionName = RegionTable::getInstance()->getLinguisticCase($region, 'п');
      $regionName = $regionName ? : $region['name'];

      $this->productCategory->seo_description = ''
        . $this->productCategory->name
        . " в {$regionName}"
        . ' с ценами и описанием.'
        . ' Купить в магазине Enter';
    }
    // keywords
    if (empty($this->productCategory->seo_keywords)) {
      $this->productCategory->seo_keywords = "{$this->productCategory->name} магазин продажа доставка {$this->getUser()->getRegion('name')} enter.ru";
    }

    $this->getResponse()->addMeta('title', $this->productCategory->seo_title);
    $this->getResponse()->addMeta('description', $this->productCategory->seo_description);
    $this->getResponse()->addMeta('keywords', $this->productCategory->seo_keywords);

    if (isset($this->productCategory) && isset($this->productCategory->seo_text)) {
      $this->setVar('article', $this->productCategory->seo_text, true);
    }

  }

  public function executeTag()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
    if (empty($this->form)) {
      $this->form = new myProductTagFormFilter(array(), array(
        'productCategory' => $this->productCategory,
        'creator' => $this->creator,
        'with_creator' => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture',)),
      ));
    }

    $this->url = $this->generateUrl('productCatalog__tag', $this->productCategory);
  }

  public function executeTag_selected()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
    $form = $this->form;
    $productCategory = $this->productCategory;

    $list = array();

    if (!isset($this->form)) {
      return sfView::NONE;
    }

    $filter = $this->getRequestParameter($this->form->getName());
    $getUrl = function ($filter, $name, $value = null) use ($productCategory, $form)
    {
      if (array_key_exists($name, $filter)) {
        if (null == $value) {
          unset($filter[$name]);
        }
        else foreach ($filter[$name] as $k => $v)
        {
          if ($v == $value) {
            unset($filter[$name][$k]);
          }
        }
      }

      $formName = $form->getName();

      return url_for('productCatalog__tag', array('productCategory' => $productCategory->token, $formName => $filter));
    };

    foreach ($this->form->getValues() as $name => $value)
    {
      if (is_array($value) ? !count($value) : empty($value)) continue;

      // цена
      if ('price' == $name) {
        $valueMin = ProductTable::getInstance()->getMinPriceByCategory($productCategory);
        $valueMax = ProductTable::getInstance()->getMaxPriceByCategory($productCategory);

        if (($value['from'] != $valueMin) || ($value['to'] != $valueMax)) {
          $list[] = array(
            'type' => 'price',
            'name' => ''
              . (($value['from'] != $valueMin) ? ('от ' . $value['from'] . ' ') : '')
              . (($value['to'] != $valueMax) ? ('до ' . $value['to'] . ' ') : '')
          ,
            'url' => $getUrl($filter, $name),
            'title' => 'Цена',
          );
        }
      }
      // производитель
      if ('creator' == $name) {
        foreach ($value as $v)
        {
          $creator = CreatorTable::getInstance()->getById($v);
          if (!$creator) continue;

          $list[] = array(
            'type' => 'creator',
            'name' => $creator->name,
            'url' => $getUrl($filter, $name, $v),
            'title' => 'Производитель',
          );
        }
      }
      // свойства товара
      else if (0 === strpos($name, 'tag-')) {
        $tagGroupId = preg_replace('/^tag-/', '', $name);
        $tagGroup = !empty($tagGroupId) ? TagGroupTable::getInstance()->getById($tagGroupId) : false;
        if (!$tagGroup) continue;

        foreach ($value as $v)
        {
          $tag = TagTable::getInstance()->getById($v);
          if (!$tag) continue;

          $list[] = array(
            'type' => 'tag',
            'name' => $tag->name,
            'url' => $getUrl($filter, $name, $tag->id),
            'title' => $tagGroup->name,
          );
        }
      }
    }

    if (0 == count($list)) {
      return sfView::NONE;
    }

    $this->setVar('list', $list);
  }

  public function executeLeftCategoryList()
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);

    $this->setVar('currentCat', $this->productCategory, true);
    $ancestorList = ProductCategoryTable::getInstance()->getAncestorList($this->productCategory, array(
      'hydrate_array' => true,
    ));

    $pathAr = array();
    if ($ancestorList)
      foreach ($ancestorList as $next) {
        $pathAr[] = $next['id'];
      }
    if (isset($ancestorList[0])) {
      $rootCat = $ancestorList[0];
    } else {
      $rootCat = $this->productCategory;
    }

    $q = ProductCategoryTable::getInstance()->createBaseQuery();
    $q->addWhere('productCategory.root_id = ?', $rootCat['id']);
    $list = $q->fetchArray();

    $isCurrent = false;
    $hasChildren = false;
    foreach ($list as $cat) {
      $fullIdList[] = $cat['id'];
      if ($cat['id'] == $this->productCategory->id) {
        $isCurrent = true;
      } elseif ($isCurrent) {
        if ($cat['level'] > $this->productCategory->level) {
          $hasChildren = true;
        } else {
          $hasChildren = false;
        }
        $isCurrent = false;
      }
    }

    $notFreeCatList = ProductCategoryTable::getInstance()->getNotEmptyCategoryList($fullIdList);
    $this->setVar('notFreeCatList', $notFreeCatList, true);
    $this->setVar('pathAr', $pathAr, true);
    $this->setVar('list', $list, true);
    $this->setVar('hasChildren', $hasChildren, true);
    $this->setVar('quantity', $this->productCategory->countProduct(), true);
  }

  public function getSiteCatTree($category, $result)
  {
    sfContext::getInstance()->getLogger()->info("call " . __METHOD__);
    if (is_object($category)) {
      $result[$category['id']]['category'] = $category;
      $result[$category['id']]['children'] = $category->getChildList(array(
          'with_filters' => false,
        )
      );
      foreach ($result[$category['id']]['children'] as $cat) {
        $result = $this->getSiteCatTree($cat, $result);
      }
    }
    return $result;
  }
}
