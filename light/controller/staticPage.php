<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 22.05.12
 * Time: 12:24
 * To change this template use File | Settings | File Templates.
 */
class staticPageController
{

  public function mainPage(Response $response, $params=array()){

    /*
     * Ищем банеры для главной страницы
     */
    $promoActions = App::getPromo()->getActivePromo();


    $productList = array();
    $categoryList = array();
    foreach($promoActions as $promoAction){ //Собираем айдишники продуктов и категорий что бы запросить у ядра инфу для генерации урлов
      foreach($promoAction->getItemList() as $item){
        if ($promoAction->getUrl()){
          continue;
        }
        else if (!$promoAction->hasItems() && !($promoAction->getTypeId() == 2))
        {
          continue;
        }
        if(1 == $item['item_type_id']){
          $productList[] = $item['item_id'];
        }
        elseif(3 == $item['item_type_id']){
          $categoryList[] = $item['item_id'];
        }
      }
    }

    $productList = array_unique($productList);
    $categoryList = array_unique($categoryList);

    /**
     * @todo перевести на асинхронный вызов, когда переведем получение категорий на v.2
     */
    $categoryData = App::getCategory()->getUrlsByIdList($categoryList);
    $categoryList = null;

    $productData = App::getProduct()->getProductPropertiesByIdList($productList, array('link', 'bar_code'));
    $productList = null;

    $promoArray = array();

    foreach($promoActions as $promoAction){
      $link = false;
      if ($promoAction->getUrl()){
        $link = $promoAction->getUrl();
      }
      else if (!$promoAction->hasItems() && !($promoAction->getTypeId() == 2))
      {
        continue;
      }
      else
      {
        $link = $this->generatePromoUrl($promoAction->getItemList(), $categoryData, $productData);
      }

      $item = array(
        'alt'   => $promoAction->getName(),
        'imgs'  => App::getPromo()->getImgUrl($promoAction->getImg(), 0),
        'imgb'  => App::getPromo()->getImgUrl($promoAction->getImg(), ($promoAction->getTypeId() == 3) ? 2 : 1),
        'url'   => $link,
        't'     => (count($promoArray) ? Config::get('bannerTimeout') : 10000),
        'ga'    => $promoAction->getId() . ' - ' . $promoAction->getName(),
      );
      if ($promoAction->getTypeId() == 3)
      {
        $item['is_exclusive'] = true;
      }
      $promoArray[] = $item;
    }

    App::getHtmlRenderer()->addCss('skin/main.css');
    $response->setContent(App::getHtmlRenderer()->renderFile('mainPage', array('promoArray' => $promoArray, 'rootCategoryList' => App::getCategory()->getRootCategoryList())));

  }

  private function generatePromoUrl($itemList, $categoryData, $productData){
    $link = '#';
    if (1 == count($itemList))
    {
      switch ($itemList[0]['item_type_id'])
      {
        case 1: //product
          $productId = $itemList[0]['item_id'];
          $link = App::getRouter()->createUrl('product.show', array('productToken' => $productData[$productId]['link']));
          break;
        case 3: //product_category
          $categoryId = $itemList[0]['item_id'];
          $link = App::getRouter()->createUrl('catalog.showCategory', array('categoryToken' => $categoryData[$categoryId]['link']));
          break;
      }
    }
    elseif (count($itemList) > 1)
    {
      $barcodeList = array();
      foreach ($itemList as $item)
      {
        $productId = $item['id'];
        if(!isset($productData[$productId])){
          continue;
        }
        if (isset($productData[$productId]['barcode']))
        {
          $barcodeList[] = $productData[$productId]['barcode'];
        }
      }
      if (count($barcodeList))
      {
        $link = App::getRouter()->createUrl('product.set', array('productBarcodeList' => implode(',', $barcodeList)), true);
      }
    }

    return $link;
  }

}
