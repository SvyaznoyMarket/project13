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

    /*
    $list[] = array(
      'name' => 'Каталог товаров',
      'url'  => url_for('@productCatalog'),
    );
    */
    if (isset($this->productCategory) && !empty($this->productCategory))
    {
      $ancestorList = $this->productCategory->getNode()->getAncestors();
      if ($ancestorList) foreach ($ancestorList as $ancestor)
      {
        $list[] = array(
          'name' => (string)$ancestor,
          'url'  => url_for('productCatalog_category', $ancestor),
        );
      }
      $list[] = array(
        'name' => (string)$this->productCategory,
        'url'  => url_for('productCatalog_category', $this->productCategory),
      );
    }
    if (isset($this->creator))
    {
      $list[] = array(
        'name' => (string)$this->creator,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
    }
    if (isset($this->product))
    {
      $list[] = array(
        'name' => (string)$this->product,
        'url'  => url_for(array('sf_route' => 'productCard', 'sf_subject' => $this->product)),
      );
    }

    $this->setVar('list', $list, false);
  }

/**
  * Executes navigation component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param Creator $creator Производитель
  */
  public function executeNavigation_seo()
  {
    $list = array();

    /*
    $list[] = array(
      'name' => 'Каталог товаров',
      'url'  => url_for('@productCatalog'),
    );
    */
    if (isset($this->productCategory) && !empty($this->productCategory))
    {
      $ancestorList = $this->productCategory->getNode()->getAncestors();
      if ($ancestorList)
      {
          foreach ($ancestorList as $ancestor)
          {
            $list[] = array(
              'name' => (string) ($ancestor['seo_header']) ? $ancestor['seo_header'] : $ancestor['name'],
              'url'  => url_for('productCatalog_category', $ancestor),
            );
          }
      }
      $list[] = array(
        'name' => (string) ($this->productCategory->seo_header) ? $this->productCategory->seo_header : $this->productCategory->name,
        'url'  => url_for('productCatalog_category', $this->productCategory),
      );
    }
    if (isset($this->creator))
    {
      $list[] = array(
        'name' => (string)$this->creator,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
    }
    if (isset($this->product))
    {
      $list[] = array(
        'name' => (string)$this->product,
        'url'  => url_for(array('sf_route' => 'productCard', 'sf_subject' => $this->product)),
      );
    }

    $this->setVar('list', $list, false);
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
        'name'  => $productCategory['name'],
        //'url'   => url_for('productCatalog_category', $productCategory),
        'level' => $productCategory['level'],
      );
    }

    $this->setVar('list', $list, true);
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
        'is_root'         => isset($this->is_root) ? $this->is_root : false,
        'with_creator'    => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )),
      ));
    }

    $this->setVar('productFilterList', $this->productCategory->FilterGroup->Filter, true);
    $this->url = url_for('productCatalog_filter', $this->productCategory);
  }
/**
  * Executes filter_selected component
  *
  * @param myProductFormFilter $form            Форма фильтра с параметрами товаров
  * @param ProductCategory     $productCategory Категория товара
  */
  public function executeFilter_selected()
  {
    $form = $this->form;
    $productCategory = $this->productCategory;

    $list = array();

    if (!isset($this->form))
    {
      return sfView::NONE;
    }

    $filter = $this->getRequestParameter($this->form->getName());
    $getUrl = function ($filter, $name, $value = null) use ($productCategory, $form) {
      if (array_key_exists($name, $filter))
      {
        if (null == $value)
        {
          unset($filter[$name]);
        }
        else foreach ($filter[$name] as $k => $v)
        {
          if ($v == $value)
          {
            unset($filter[$name][$k]);
          }
        }
      }

      $formName = $form->getName();

      return url_for('productCatalog_filter', array('productCategory' => $productCategory->token, $formName => $filter));
    };

    foreach ($this->form->getValues() as $name => $value)
    {
      if (is_array($value) ? !count($value) : empty($value)) continue;

      // цена
      if ('price' == $name)
      {
        $valueMin = ProductTable::getInstance()->getMinPriceByCategory($productCategory);
        $valueMax = ProductTable::getInstance()->getMaxPriceByCategory($productCategory);

        if (($value['from'] != $valueMin) || ($value['to'] != $valueMax))
        {
          $list[] = array(
            'name' => ''
              .(($value['from'] != $valueMin) ? ('от '.$value['from'].' ') : '')
              .(($value['to'] != $valueMax) ? ('до '.$value['to'].' ') : '')
              .'&nbsp;<span class="rubl">p</span>'
            ,
            'url'   => $getUrl($filter, $name),
            'title' => 'Цена',
          );
        }
      }
      // производитель
      if ('creator' == $name)
      {
        foreach ($value as $v)
        {
          $creator = CreatorTable::getInstance()->getById($v);
          if (!$creator) continue;

          $list[] = array(
            'name' => $creator->name,
            'url'  => $getUrl($filter, $name, $v),
            'title' => 'Производитель',
          );
        }
      }
      // свойства товара
      else if (0 === strpos($name, 'param-'))
      {
        $filterId = preg_replace('/^param-/', '', $name);
        $productFilter = !empty($filterId) ? ProductFilterTable::getInstance()->getById($filterId) : false;
        if (!$productFilter) continue;

        switch ($productFilter->type)
        {
          case 'range':
            if (($value['from'] != $productFilter->value_min) || ($value['to'] != $productFilter->value_max))
            {
              $list[] = array(
                'name' => ''
                  .(($value['from'] != $productFilter->value_min) ? ('от '.$value['from'].' ') : '')
                  .(($value['to'] != $productFilter->value_max) ? ('до '.$value['to'].' ') : '')
                  .($productFilter->Property->unit ? $productFilter->Property->unit : '')
                ,
                'url'  => $getUrl($filter, $name),
                'title' => $productFilter->name,
              );
            }
            break;
          case 'choice':
            foreach ($value as $v)
            {
              $productPropertyOption = ProductPropertyOptionTable::getInstance()->getById($v);
              if (!$productPropertyOption) continue;

              $list[] = array(
                'name' =>
                  in_array($productPropertyOption->value, array('да', 'нет'))
                  ? $productFilter->name.': '.$productPropertyOption->value
                  : $productPropertyOption->value
                ,
                'url'  => $getUrl($filter, $name, $productPropertyOption->id),
                'title' => $productFilter->name,
              );
            }
            break;
          case 'checkbox':
            if ((null !== $value) && (1 == count($value)))
            {
              $list[] = array(
                'name' => $productFilter->name.': '.($value[0] ? 'да' : 'нет'),
                'url'  => $getUrl($filter, $name),
                'title' => '',
                'title' => $productFilter->name,
              );
            }

            break;
        }
      }
    }
    //myDebug::dump($list);

    if (0 == count($list))
    {
      return sfView::NONE;
    }

    $this->setVar('list', $list, true);
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

  public function executeArticle_seo()
  {
    $this->getResponse()->addMeta('title',$this->productCategory->seo_title);
    $this->getResponse()->addMeta('description',$this->productCategory->seo_description);
    $this->getResponse()->addMeta('keywords',$this->productCategory->seo_keywords);

    if (isset($this->productCategory) && isset($this->productCategory->seo_text)) {
        $this->setVar('article', $this->productCategory->seo_text, true);
    }

  }

  /**
  * Executes tag component
  *
  * @param myProductTagFormFilter $form Форма фильтров
  */
  public function executeTag()
  {
    if (empty($this->form))
    {
      $this->form = new myProductTagFormFilter(array(), array(
        'productCategory' => $this->productCategory,
        'creator'         => $this->creator,
        'with_creator'    => !in_array($this->productCategory->getRootCategory()->token, array('jewel', 'furniture', )),
      ));
    }

    $this->url = url_for('productCatalog_tag', $this->productCategory);
  }

/**
  * Executes filter_selected component
  *
  * @param myProductFormFilter $form            Форма фильтра с параметрами товаров
  * @param ProductCategory     $productCategory Категория товара
  */
  public function executeTag_selected()
  {
    $form = $this->form;
    $productCategory = $this->productCategory;

    $list = array();

    if (!isset($this->form))
    {
      return sfView::NONE;
    }

    $filter = $this->getRequestParameter($this->form->getName());
    $getUrl = function ($filter, $name, $value = null) use ($productCategory, $form) {
      if (array_key_exists($name, $filter))
      {
        if (null == $value)
        {
          unset($filter[$name]);
        }
        else foreach ($filter[$name] as $k => $v)
        {
          if ($v == $value)
          {
            unset($filter[$name][$k]);
          }
        }
      }

      $formName = $form->getName();

      return url_for('productCatalog_tag', array('productCategory' => $productCategory->token, $formName => $filter));
    };

    foreach ($this->form->getValues() as $name => $value)
    {
      if (is_array($value) ? !count($value) : empty($value)) continue;

      // цена
      if ('price' == $name)
      {
        $valueMin = ProductTable::getInstance()->getMinPriceByCategory($productCategory);
        $valueMax = ProductTable::getInstance()->getMaxPriceByCategory($productCategory);

        if (($value['from'] != $valueMin) || ($value['to'] != $valueMax))
        {
          $list[] = array(
            'name' => ''
              .(($value['from'] != $valueMin) ? ('от '.$value['from'].' ') : '')
              .(($value['to'] != $valueMax) ? ('до '.$value['to'].' ') : '')
              .'&nbsp;<span class="rubl">p</span>'
            ,
            'url'   => $getUrl($filter, $name),
            'title' => 'Цена',
          );
        }
      }
      // производитель
      if ('creator' == $name)
      {
        foreach ($value as $v)
        {
          $creator = CreatorTable::getInstance()->getById($v);
          if (!$creator) continue;

          $list[] = array(
            'name' => $creator->name,
            'url'  => $getUrl($filter, $name, $v),
            'title' => 'Производитель',
          );
        }
      }
      // свойства товара
      else if (0 === strpos($name, 'tag-'))
      {
        $tagGroupId = preg_replace('/^tag-/', '', $name);
        $tagGroup = !empty($tagGroupId) ? TagGroupTable::getInstance()->getById($tagGroupId) : false;
        if (!$tagGroup) continue;

        foreach ($value as $v)
        {
          $tag = TagTable::getInstance()->getById($v);
          if (!$tag) continue;

          $list[] = array(
            'name'  => $tag->name,
            'url'   => $getUrl($filter, $name, $tag->id),
            'title' => $tagGroup->name,
          );
        }
      }
    }
    //myDebug::dump($list);

    if (0 == count($list))
    {
      return sfView::NONE;
    }

    $this->setVar('list', $list, true);
  }

  public function executeLeftCategoryList(){


    $this->setVar('currentCat', $this->productCategory, true);
    $ancestorList = $this->productCategory->getNode()->getAncestors();

    $pathAr = array();
    if ($ancestorList)
    foreach($ancestorList as $next) {
        $pathAr[] = $next['id'];
    }
    if (isset($ancestorList[0])) {
        $rootCat = $ancestorList[0];
    } else {
        $rootCat = $this->productCategory;
    }

    $q = ProductCategoryTable::getInstance()->createBaseQuery();
    $q->addWhere('productCategory.root_id = ?', $rootCat->id);
    $list = $q->fetchArray();

    $isCurrent = false;
    foreach($list as $cat) {
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
    #myDebug::dump($productCountlist);
    $this->setVar('notFreeCatList', $notFreeCatList, true);
    $this->setVar('pathAr', $pathAr, true);
    $this->setVar('list', $list, true);
    $this->setVar('hasChildren', $hasChildren, true);
    $this->setVar('quantity', $this->productCategory->countProduct(), true);


    /*
    $this->setVar('currentCat', $this->productCategory, true);
    $ancestorList = $this->productCategory->getNode()->getAncestors();

    if (isset($ancestorList[0])) {
        $rootCat = $ancestorList[0];
    } else {
        $rootCat = $this->productCategory;
    }
    $tree = $this->getSiteCatTree($rootCat, array());
    $this->setVar('ancestorList', $ancestorList, true);
    $this->setVar('root_info', $rootCat, true);
    $this->setVar('root_list', $tree[ $rootCat['id'] ], true);
    $this->setVar('currentDirectory', $this->productCategory, true);
    $this->setVar('tree', $tree, true);

    $this->setVar('quantity', $this->productCategory->countProduct(), true);
*/
  }


  public function getSiteCatTree($category, $result){
        if (is_object($category)) {
            $result[$category['id']]['category'] = $category;
            $result[$category['id']]['children'] = $category->getChildList(array(
                //'select'       => 'productCategory.id,productCategory.core_id, productCategory.name, productCategory.token',
                'with_filters' => false,
                )
            );
            foreach($result[$category['id']]['children'] as $cat){
                $result = $this->getSiteCatTree($cat, $result);
            }
        }
        return $result;
  }
}
