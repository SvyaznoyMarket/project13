<?php

/**
 * search components.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property string               $searchString Поисковая фраза
 * @property myDoctrineCollection $productTypeList Коллекция типов товаров
 * @property ProductType          $productType     Выбранный тип товара
 */
class searchComponents extends myComponents
{
 /**
  * Executes navigation component
  *
  *
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => "Поиск (".$this->searchString.")",
      'url'  => $this->generateUrl('search', array('searchString' => $this->searchString)),
    );

    $this->setVar('list', $list, false);
  }

    public function executeFilter_productType()
    {
        $list = array(
            'first' => array(),
            'other' => array(),
        );

        if(!$this->productTypeList){
            return sfView::NONE;
        }

        # Загрузили категории типов
        $this->productTypeList->loadRelated('ProductCategory');
        $firstProductCategory = Null;

        # Побежали по списку типов
        foreach ($this->productTypeList as $i => $productType)
        {
            $index = 'other';

            # Побежали по списку категорий текущего типа
            foreach ($productType->ProductCategory as $productCategory)
            {
                # Если текущая категория неактивна - пропускаем
                if(!is_object($productCategory) || !$productCategory->is_active)
                {
                    continue;
                }

                # Если не выбрана главная категория и рутовая категория текущей категории активна - отмечаем ее как главную
                if(is_null($firstProductCategory) && $productCategory->getRootCategory()->is_active)
                {
                    $firstProductCategory = $productCategory->getRootCategory();
                }

                # Если ID рутовой категории текущей категории равен ID главной категории
                if ($productCategory->getRootCategory()->id == $firstProductCategory->id)
                {
                    # Отметили индекс как первый
                    $index = 'first';
                    break;
                }
            }

            # Записали информацию в список
            $list[$index][] = array(
                'url'      => $this->generateUrl('search', array('q' => $this->searchString, 'product_type' => $productType->id)),
                'name'     => (string)$productType,
                'token'    => $productType->id,
                'count'    => isset($productType->_product_count) ? $productType->_product_count : 0,
                'value'    => $productType->id,
                'selected' => ((0 == $i) && !$this->productType) || ($this->productType && ($this->productType->id == $productType->id)),
            );
        }

        # Если к этому моменту не удалось определить главную категорию, ставим заглушку
        if(is_null($firstProductCategory))
        {
            $firstProductCategory = new ProductCategory();
        }

        $this->setVar('list', $list, true);
        $this->setVar('firstProductCategory', $firstProductCategory, true);

        return sfView::SUCCESS;
    }
}
