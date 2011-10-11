<?php

/**
 * userProductRating actions.
 *
 * @package    enter
 * @subpackage userProductRating
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductRatingActions extends myActions
{
    
   private $_request;
   
   private $_product;
   
   private $_validateResult;
    
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $user = $this->getUser();
    $this->redirectUnless($user->isAuthenticated(), '@user_signin');

    $product = $this->getRoute()->getObject();

    if (is_array($request['rating']))
    {
      $productRatingType = ProductRatingTypeTable::getInstance()->getById($product->Type->rating_type_id);
      foreach ($productRatingType->Property as $productRatingTypeProperty)
      {
        $value = isset($request['rating'][$productRatingTypeProperty->id]) ? (float)$request['rating'][$productRatingTypeProperty->id] : false;
        if (false !== $value)
        {
          $userProductRating = new UserProductRating();
          $userProductRating->fromArray(array(
            'user_id'     => $user->getGuardUser()->id,
            'property_id' => $productRatingTypeProperty->id,
            'product_id'  => $product->id,
            'value'       => $value,
          ));
          $userProductRating->replace();
        }
      }
    }

    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'success' => true,
        'data'    => array(
          'content' => $this->getComponent($this->getModuleName(), 'show', array('product' => $product)),
        ),
      ));
    }
    $this->redirect($request->getReferer());
  }
  
  
  
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreatetotal(sfWebRequest $request)
  {
        
        $this->_request = $request;

        try{
          //производим валидацию входящих данных
          $this->_validateCreateTotalData();
        }
        catch(Exception $e){
          $this->_validateResult['success'] = false;
          $this->_validateResult['error'] = "Неверные данные";
        }

        //оповещаем об ошибке
        if (!$this->_validateResult['success']){
          return $this->_refuse();      
        }


        $user = $this->getUser();             

        //если пользователь авторизован
        //if (isset($user) && $user->isAuthenticated()){

            //посмотрим в локальной базе. Вероятно, пользователь уже голосовал за этот товар, и информация есть здесь
            $table = UserProductRatingTotalTable::getInstance();
            $existItems = $table->getQueryObject()->andWhere('product_id="'.$this->_product->id.'"')->fetchArray();
            //print_r($existItems);
            if (count($existItems)>0){
              $this->_validateResult['success'] = false;
              $this->_validateResult['error'] = "Вы уже голосовали за этот товар, и не можете проголосовать повторно.";            
              //return $this->_refuse();
            }
            
            //отправляем запос на голосование в ядро
            $core = Core::getInstance();
            $ratingInfo = $core->query('/user/product/rating/create/',array(),array('product_id'=>$this->_product->id,'user_id'=>1,'value'=>$this->_request['rating'],'criterion_id'=>1));
            //если от ядра был получен отказ на запись данных
            if (!$ratingInfo){
              $this->_validateResult['success'] = false;
              $this->_validateResult['error'] = $core->getError();            
              return $this->_refuse();            
            }

            //пользователь авторизован, но не голосовал
            //добавляем голос пользователя
            $userRate = new UserProductRatingTotal();
            $userRate->fromArray(array('product_id'=>$this->_product->id,'user_id'=>$user->id,'value'=>$this->_request['rating']));
            $userRate->save();
       // }
        //если пользователь не авторизован, позволяем ему голосовать в любом случае 
        //TODO: проверка неавторизованного пользователя

        //пересчитываем рейтинг товара
        $this->_product->rating;
        $this->_product->rating_quantity;
        $currentRatingFull = $this->_product->rating * $this->_product->rating_quantity;
        $this->_product->rating_quantity++;
        $currentRatingFull += $this->_request['rating'];
        $this->_product->rating = $currentRatingFull / $this->_product->rating_quantity;
        $this->_product->save();


        //отправляем ответ - голосование прошло успешно
        if ($request->isXmlHttpRequest())
        {
          return $this->renderJson(array(
            'success' => true,
            'data'    => array(
              'rating' => $this->_product->rating,      //текущий рейтинг
              'rating_quantity' => $this->_product->rating_quantity,    //количество проголосовавших
            ),
          ));
        }
        $this->redirect($request->getReferer());
  }
  
  
  private function _refuse(){
      return $this->renderJson(array(
        'success' => $this->_validateResult['success'],
        'data'    => array(
          'error' => $this->_validateResult['error'],
        ),
      ));        
  }
  
  private function _validateCreateTotalData()
  {
        $result['success'] = true;  

        //ищем подукт
        //если передан token продукта
        if (isset($request['product']))
        {
            $this->_product = ProductTable::getInstance()->findOneByToken($this->_request['product']);
        }
        //иначе создаём для того продукта, на странице которого находимся
        if (!isset($this->_product))
        {
            $this->_product = $this->getRoute()->getObject();        
        }
        if (!$this->_product)
        {
            $result['success'] = false;  
            $result['error'][] = "Товар не найден";  
        }
        //значение рейтинга
        if (!in_array($this->_request['rating'],array(1,2,3,4,5)))
        {
             $result['success'] = false;  
             $result['error'][] = 'Значение рейтинга должно быть от 1 до 5';  
        }      
        $this->_validateResult = $result;
  }
  
 
}

