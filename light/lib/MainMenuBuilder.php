<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 13:09
 * To change this template use File | Settings | File Templates.
 */
class MainMenuBuilder
{

  /**
   * @static
   * @param array $categories
   * @param int $quantity
   */
  public static function getContainers($categories, $quantity=4){
    $weights = self::getWeights($categories);
    $boxes = self::getCapacityOfBoxes($weights, $quantity);
    $return = array();
    unset($weights[0]);

    $categoryInfo = array();
    foreach($categories as $category){
      $categoryInfo[$category['id']] = array('link' => $category['link'], 'name' => $category['name'], 'children' => array());
      if(isset($category['children'])){
        foreach($category['children'] as $subCategory){
          $categoryInfo[$category['id']]['children'][] = array('link' => $subCategory['link'], 'name' => $subCategory['name']);
        }
      }
    }
    unset($categories);

    arsort($weights); //Сортируем по убыванию для лучших результатов

    foreach($weights as $categoryId => $categoryWeight){
      foreach($boxes as $boxNum => $boxCapacity){
        if($boxCapacity >= $categoryWeight){
          if(!isset($return[$boxNum])){
            $return[$boxNum] = array();
          }
          $return[$boxNum][] = $categoryInfo[$categoryId];
          unset($weights[$categoryId]);
          $boxes[$boxNum] -= $categoryWeight;
          break;
        }
      }
    }

    /**
     * Если что-то осталось нераспределенным - пихаем в менее наполненную
     */
    foreach($weights as $categoryId => $categoryWeight){
      $max = 0;
      $boxNum = 0;
      foreach($boxes as $boxNumber => $boxWeight){
        if($boxWeight > $max){
          $boxNum = $boxNumber;
        }
      }

      $return[$boxNum][] = $categoryInfo[$categoryId];
      unset($weights[$categoryId]);
      $boxes[$boxNum] -= $categoryWeight;
    }

    return $return;
  }

  /**
   * @static Функция считает вес категории исходя из количества подкатегорий в ней (если они есть)
   * и длины имени категории (проверяется, не занимает ли она 2 строки)
   * @param array $categories
   * @return array categoryId => categoryWeight //считаются и родительские и дочерние
   */
  public static function getWeights($categories){
    $return = array();
    $return[0] = 0;

    foreach($categories as $category){
      $return[$category['id']] = 0;
      if($category['level'] == 2){
        if(strlen($category['name']) > 24){
          $return[$category['id']] += 5; //Переползает на 2 строки
        }
        else{
          $return[$category['id']] += 3;
        }
      }
      if($category['level'] == 3){
        if(strlen($category['name']) > 40){
          $return[$category['id']] += 2; //Переползает на 2 строки
        }
        else{
          $return[$category['id']] += 1;
        }
      }
      if (isset($category['children'])){
        $return[$category['id']] += count($category['children']);
      }
      $return[0] += $return[$category['id']];
    }
    return $return;
  }

  /**
   * Функция на основе информации о весах категорий расчитывает размеры блоков.
   * Разбивать дочерние категории в разные блоки нельзя
   *
   * @param array $weights
   * @param int $quantity
   * @return array box sizes
   */
  private static function getCapacityOfBoxes($weights, $quantity){
    $weight = (int) $weights[0]*1.1; //Чуть увеличиваем раздел блоков что бы из-за переносов последний блок не распирало
    unset($weights[0]);
    $middle = ceil($weight/$quantity);

    $blocks = array();
    for($i=0; $i<$quantity; $i++){
      $max = max($weights);
      if($middle < $max){ //Проверка на категории, не влезающие в блоки даже если они одни
        $blocks[] = $max;
        $weight -= $max;
        $middle = ceil($weight/($quantity-$i)); //расчитываем новое значение среднего размера блока
        foreach($weights as $key => $val){
          if($val == $max){
            unset($weights[$key]);
            break;
          }
        }
      }
      else{
        $blocks[] = $middle;
        $weight -= $max;
      }
    }
    return $blocks;
  }
}
