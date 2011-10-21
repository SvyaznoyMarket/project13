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

      $colomnsArr = array();
      //распределяем эти разделы на равные столбцы
      foreach($countResult as $mainCatId => $mainCatList){
          $catIdList = array_keys($mainCatList);
          /*
          switch(count($mainCatList)){

              case 1:
                  if ($mainCatList[ $catIdList[0] ]<=15)
                  {
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                      'num' => $mainCatList[ $catIdList[0] ]));
                  }
                  elseif($mainCatList[ $catIdList[0] ]<=30)
                  {
                    $first = round($mainCatList[ $catIdList[0] ]/2);
                    $second = $mainCatList[ $catIdList[0] ] - $first;
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                      'num' => $first
                                                ));
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                      'num' => $second
                                                ));
                  }
                  break;
              case 2:
                  if ($mainCatList[ $catIdList[0] ]<=15)
                  {
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                        'num' => $mainCatList[ $catIdList[0] ]));
                  }
                  else
                  {
                    $first = round($mainCatList[ $catIdList[0] ]/2);
                    $second = $mainCatList[ $catIdList[0] ] - $first;
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                      'num' => $first
                                                ));
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[0],
                                                      'num' => $second
                                                ));
                  }
                  if ($mainCatList[ $catIdList[1] ]<=15)
                  {
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[1],
                                                        'num' => $mainCatList[ $catIdList[1] ]));
                  }
                  else
                  {
                    $first = round($mainCatList[ $catIdList[1] ]/2);
                    $second = $mainCatList[ $catIdList[1] ] - $first;
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[1],
                                                      'num' => $first
                                                ));
                    $colomnsArr[$mainCatId][] = array(array('id' => $catIdList[1],
                                                      'num' => $second
                                                ));
                  }
                  break;

             default:
           * */

                 $fullColumnNum = 4;        //количество колонок
                 $avrHeight = 0;
                 foreach($mainCatList as $num) $avrHeight += $num;
                 //чуть увеличим среднее, чтоб в последней колонке не собиралось много элементов
                 $avrHeight = round($avrHeight/4); // + $avrHeight/4/100*10);   //средняя (идеальная) высота колонки
                 #echo $avrHeight .'   = '.$mainCatId.'--$avrHeight<br>';

                 $minHeight = $avrHeight - $avrHeight/100*30;   //максимальная -критичная высота. Отклонение 30%
                 $maxHeight = $avrHeight + $avrHeight/100*30;   //минимальная -критичная высота. Отклонение 30%

                 $useFierst = 0;
                 //
                 //
                 //
                 //
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


                     //слудующий шаг. оцениваем, на сколько заполнилась колонка
                     //длинна больше среднего - точно закончили с этой колонкой
                     if ($writtenNum>=$avrHeight) continue;
                     //длинна меньше среднего - поищем что-либо
                     else{
                         //сколько максимум в эту колонку можно дописать строк
                         $maxFreePlace = $maxHeight - $writtenNum;
                         $freePlace = $avrHeight - $writtenNum;
                         #echo $freePlace .'--$freePlace<br>';
                         //ищем категорию с такой длинной
                         $goodId = 0;
                         $ratherGoodId = 0;
                         foreach($mainCatList as $id => $num){
                             if ($num==0) continue;
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
                         if (!$goodId) continue;
                         //добавляем найденый элемент в колонку
                         $colomnsArr[$mainCatId][$columnNum][] = array('id' => $goodId,
                                                                       'num' => $mainCatList[ $goodId ]);
                         unset($mainCatList[ $goodId ]);    //удаляем элемент из общего списка - у него уже есть место

                     }
                 }
                 #print_r($mainCatList);
                 if (count($mainCatList))
                 foreach($mainCatList as $catId => $catNum)
                     $colomnsArr[$mainCatId][3][] = array('id' => $catId,
                                                                   'num' => $catNum);

              //   break;


          //}
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

    $this->setVar('productCategoryList', $this->productCategory->getNode()->getChildren());
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
      'name'             => (string)$this->productCategory,
      'url'              => url_for('productCatalog_category', $this->productCategory),
	  'carousel_data_url'=> url_for('productCatalog_carousel', $this->productCategory),
      'product_quantity' => $this->productCategory->countProduct(),
	  'links'            => $this->productCategory->getLink(),
    );

    if ('carousel' == $this->view)
    {
      if (0 == $item['product_quantity'])
      {
        return sfView::NONE;
      }

      $item['product_list'] = ProductTable::getInstance()->getListByCategory($this->productCategory, array(
        'limit' => 6,
      ));
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
}
