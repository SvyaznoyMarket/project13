<?php

/**
 * productCategory components.
 *
 * @package    enter
 * @subpackage productCategory
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCategoryComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param Doctrine_Collection $productCategoryList Коллекция категорий товаров
  * @param view $view Вид
  */
  public function executeList()
  {
    if (!in_array($this->view, array('default', 'carousel', 'preview')))
    {
      $this->view = 'default';
    }

    $list = array();
    foreach ($this->productCategoryList as $productCategory)
    {
      $list[] = array(
        'name'            => (string)$productCategory,
        'productCategory' => $productCategory,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes root_list component
  *
  * @param ProductCategory $productCategory Текущая категория товара
  */
  public function executeRoot_list()
  {
//    $list = array();
//    foreach (ProductCategoryTable::getInstance()->getRootList() as $productCategory)
//    {
//      $list[] = array(
//        'name' => (string)$productCategory,
//        'url'  => url_for('productCatalog_category', $productCategory),
//      );
//    }

    $this->setVar('list', ProductCategoryTable::getInstance()->getRootList(), true);
  }

  public function executeExtra_menu()
  {
    /*
	  $data = ProductCategoryTable::getInstance()->getDescendatList(null, array(
      'select'    => 'productCategory.id, productCategory.core_id, productCategory.token, productCategory.name',
      'min_level' => 1,
      'max_level' => 2,
      'with_filters' => false
    ));
    */
	  $data = ProductCategoryTable::getInstance()->getSubList();
	  $result = array();

	  foreach ($data as $row) {
          #echo $row['id'] .'  '.$row['name'].'----'.$row['level'] .'---<br>';
          $coreIdToId[ $row['core_id'] ] = $row['id'];
          $rootCat[ $row['id'] ] = $row;
          $catTreeTmp[ $row->core_id ]['id'] = $row->id;
          $catTreeTmp[ $row->core_parent_id ]['list'][] = $row;

		  if (!isset($result[$row->root_id])) {
			  $result[$row->root_id] = array();
		  }
		  $result[$row->root_id][] = $row;
	  }
     # exit();

      foreach($catTreeTmp as $item){
          if (isset($item['id']) && isset($item['list'])) $catTree[ $item['id'] ] = $item['list'];
      }


      //генерируем массив количеств разделов
      foreach($result as $i => $cat){
          foreach($cat as $c){
              #echo $c['name'].'====';
              if (isset($c['id']) && !isset($countResult[$i][ $c['id'] ]) && $c['level']==1) $countResult[$i][ $c['id'] ] = 0;
              if (!isset($coreIdToId[ $c['core_parent_id'] ]) || !isset($c['core_parent_id'])) continue;
              if (!isset($countResult[$i][ $coreIdToId[ $c['core_parent_id'] ] ]) && isset($c['core_parent_id']) && isset($coreIdToId[ $c['core_parent_id'] ]) ) $countResult[$i][ $coreIdToId[ $c['core_parent_id'] ] ] = 1;
              else $countResult[$i][ $coreIdToId[ $c['core_parent_id'] ] ]++;
          }
      }

      //к каждому элементу добавляем 2 - это условная высота заголовка большой категории
      $sizeofWideElement = 2;
      foreach($countResult as $n => $big){
          foreach($big as $m => $small){
              $countResult[$n][$m] = $countResult[$n][$m] + $sizeofWideElement;
          }
      }

      $colomnsArr = array();
      //распределяем эти разделы на равные столбцы
      foreach($countResult as $mainCatId => $mainCatList){
          $catIdList = array_keys($mainCatList);


         $fullColumnNum = 4;        //количество колонок
         $avrHeight = 0;
         foreach($mainCatList as $num) $avrHeight += $num;
         //чуть увеличим среднее, чтоб в последней колонке не собиралось много элементов
         $avrHeight = round($avrHeight/4); // + $avrHeight/4/100*10);   //средняя (идеальная) высота колонки
         #echo $avrHeight .'   = '.$mainCatId.'--$avrHeight<br>';

         $minHeight = round( $avrHeight - $avrHeight/100*30);   //максимальная -критичная высота. Отклонение 30%
         $maxHeight = round($avrHeight + $avrHeight/100*30);   //минимальная -критичная высота. Отклонение 30%

         $useFierst = 0;



         //print_r($mainCatList);
         //проходим по колонкам
         unset($mainCatList['']);
         for ($columnNum=0; $columnNum<$fullColumnNum; $columnNum++){

             if (count($mainCatList)==0) break;

             //если есть не законченная, используем её
             if ($useFierst){
                 $currentMaxId = $useFierst;
                 $useFierst = 0;
                 $writtenNum = $mainCatList[ $currentMaxId ] - 1;
             }
             else{
                 //выбираем самую длинную на данный момент категорию
                 $currentMaxId = 0;
                 $currentMaxNum = 0;
                 foreach($mainCatList as $id => $num){
                     if ($num>$currentMaxNum){
                         $currentMaxNum = $num;
                         $currentMaxId = $id;
                     }
                 }
                 $writtenNum = $mainCatList[ $currentMaxId ];
             }
             //если нельзя делать такую длинную колонку - разрежем
             if ($writtenNum>$maxHeight){ // && ($writtenNum-$maxHeight)>1){
                 $writtenNum = $avrHeight;
                 $mainCatList[ $currentMaxId ] = $writtenNum;
                 $useFierst = $currentMaxId;
                 //записываем самую длинную, как самую первую в колонке
                 $colomnsArr[$mainCatId][$columnNum][] = array('id' => $currentMaxId,
                                                               'num' => $writtenNum);
                // $writtenNum = $mainCatList[ $currentMaxId ] - $writtenNum;
                 continue;  //уходим к след. колонке
             }
             else{
                 unset($mainCatList[ $currentMaxId ]);    //удаляем элемент из общего списка - у него уже есть место
                 //записываем самую длинную, как самую первую в колонке
                 $colomnsArr[$mainCatId][$columnNum][] = array('id' => $currentMaxId,
                                                               'num' => $writtenNum);
             }


             $fullColumnWrittenNum = $writtenNum;   //сколько уже есть в колонке
             $tryToFind = true;
             //если колонка ещё не заполненна до упора
             while($fullColumnWrittenNum<$avrHeight && $tryToFind)
             {
                 //сколько максимум в эту колонку можно дописать строк
                 $maxFreePlace = $maxHeight - $fullColumnWrittenNum;
                 $freePlace = $avrHeight - $fullColumnWrittenNum;
                 #echo $freePlace .'--$freePlace<br>';
                 //ищем категорию с такой длинной
                 $goodId = 0;
                 $ratherGoodId = 0;
                 foreach($mainCatList as $id => $num){
                     if ($num<=$sizeofWideElement) continue;
                     //идеально!
                     if ($num<=$freePlace){
                         $goodId = $id;
                         break;
                     }
                     //не идеально, но сойдёт
                     elseif ($num<=$maxFreePlace){
                         $ratherGoodId = $id;
                     }
                 }
                 if (!$goodId)
                     $goodId = $ratherGoodId;
                 //больше нечего добавить в эту колонку.
                 if (!$goodId){
                     //в эту колонку больше нечего добавить. не пытаемся.
                     $tryToFind = false;
                     continue;
                 }
                 //добавляем найденый элемент в колонку
                 $colomnsArr[$mainCatId][$columnNum][] = array('id' => $goodId,
                                                               'num' => $mainCatList[ $goodId ]);
                 $fullColumnWrittenNum += $mainCatList[ $goodId ];
                 unset($mainCatList[ $goodId ]);    //удаляем элемент из общего списка - у него уже есть место
             }
         }
         #print_r($mainCatList);
         //всё, что осталось дописываем в последнюю колонку.
         //все категории без подкатегорий остались здесь
         if (count($mainCatList))
         foreach($mainCatList as $catId => $catNum)
             $colomnsArr[$mainCatId][ $fullColumnNum-1 ][] = array('id' => $catId,
                                                           'num' => $catNum);


      }


      /*
      echo '<pre>';
      #print_r($catTreeTmp);
      #print_r($catTree);
      #print_r($countResult);
      print_r($colomnsArr);
      echo '</pre>';
      #exit();   */




	  $this->setVar('catTree', $catTree, true);
	  $this->setVar('rootlist', $result, true);
	  $this->setVar('rootCat', $rootCat, true);
	  $this->setVar('colomnsArr', $colomnsArr, true);
  }



 /**
  * Executes child_list component
  *
  * @param ProductCategory $productCategory Родительская категория товара
  * @param view $view Вид
  */
  public function executeChild_list()
  {
    if (!$this->view)
    {
      $this->view = 'default';
    }

    $this->setVar('productCategoryList', $this->productCategory->getChildList(array(
      'select'       => 'productCategory.id, productCategory.name, productCategory.token',
      'with_filters' => false,
    )));
  }
 /**
  * Executes show component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param view $view Вид
  */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'carousel', 'preview')))
    {
      $this->view = 'default';
    }

    $item = array(
      'name'              => (string)$this->productCategory,
      'root_name'         => (string)$this->productCategory->getRootCategory(),
      'url'               => url_for('productCatalog_category', $this->productCategory),
      'carousel_data_url' => url_for('productCatalog_carousel', $this->productCategory),
      'product_quantity'  => $this->productCategory->countProduct(array('view' => 'list', )),
      'links'             => $this->productCategory->getLinkList(),
      'has_line'          => $this->productCategory->has_line,
    );

    if ('carousel' == $this->view)
    {
      if (0 == $item['product_quantity'])
      {
        return sfView::NONE;
      }

      $item['product_list'] = ProductTable::getInstance()->getListByCategory($this->productCategory, array('with_properties' => false, 'limit' => 6, 'view' => 'list', ));
    }
    if ('preview' == $this->view)
    {
      if (0 == $item['product_quantity'])
      {
        return sfView::NONE;
      }

      $item['photo'] = $this->productCategory->getPhotoUrl();
    }

    $this->setVar('item', $item, true);
  }
  /**
  * Executes productType_list component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeProductType_list()
  {
    $list = array();
    foreach (ProductTypeTable::getInstance()->getListByProductCategory($this->productCategory, array(
      'select'            => 'productType.id, productType.name',
      'group'             => 'productType.id, productType.name',
      'order'             => 'productType.name',
      'with_productCount' => true,
    )) as $productType) {
      if (0 == $productType->product_count) continue;

      $list[] = array(
        'name'             => $productType->name,
        'url'              => url_for(array('sf_route' => 'productCatalog_productType', 'sf_subject' => $this->productCategory, 'productType' => $productType->id)),
        'product_quantity' => $productType->product_count,
      );
    }

    $this->setVar('table', myToolkit::groupByColumn($list, 4), true);
  }
}
