<?php

/**
 * api actions.
 *
 * @package    enter
 * @subpackage api
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends myActions
{
   
    
  /**
   * Сбрасывает кеш сущности.
   * 
   * @param sfWebRequest $request
   *  - type = [product, product_category]
   *  - id - id сущности
   */
  public function executeCacheClean(sfWebRequest $request)
  {
      #echo 'clean cash';
      #exit();
      
    try {
      $response = trim(file_get_contents('php://input'));

      $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getLogFilename()));
      $logger->log('Response: '.$response);

      $data = json_decode($response, true);
      if (!isset($data['type']) || !isset($data['id'])) {
          $logger->err('Получен некорректный запрос на очистку кеша.');          
          return $this->renderJson(array(
            'confirmed' => false,
          ));          
      }
      if ($data['type'] == 'product') {
          $productTable = ProductTable::getInstance();
          $product = $productTable->findOneBy('core_id', $data['id']);
          #myDebug::dump($product);
          CacheEraser::getInstance()->erase($product->getTable()->getCacheEraserKeys($product, 'show'));          
      } else {
          return $this->renderJson(array(
            'confirmed' => false,
          ));          
      }
      //очищаем кеш сущности
      #print_r($data);
      
      return $this->renderJson(array(
        'confirmed' => true,
      ));
      
    }
    catch (Exception $e) {
      $logger->err($e->getCode().' - '.$e->getMessage());
      return $this->renderJson(array(
        'confirmed' => false,
      ));          
    }      
  }    
    
  
  /**
   * Возвращает статус пакета синхранизации
   * @param sfWebRequest $request
   * @return int 
   */
  public function executePacketStatus(sfWebRequest $request)
  {
    try {
      $response = trim(file_get_contents('php://input'));

      $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getLogFilename()));
      $logger->log('Response: '.$response);

      $data = json_decode($response, true);
      #$data['id'] = 85318;
      if (!isset($data['id'])) {
          $logger->err('Ошибка. В запросе статуса пакета не передан id пакета.');          
          return $this->renderJson(array(
            'confirmed' => false,
          ));          
      }
      #print_r($data);
      
      $table = TaskTable::getInstance();
      $packetInfo = $table->getQueryObject()
              ->addWhere('core_packet_id = ?', $data['id'])
              ->fetchArray()
            ;
      if (isset($packetInfo[0]) && isset($packetInfo[0]['status'])) {
          if ($packetInfo[0]['status'] == 'success') {
              $status = 1;
          } elseif ($packetInfo[0]['status'] == 'fail') {
              $status = 2;
          } else {
              $status = 0;
          }
      } else {
          $status = 0;          
      }
      #echo $status .'==$status';
      return $this->renderJson(array(
        'confirmed' => true,
        'status' => $status  
      ));
      
    }
    catch (Exception $e) {
      $logger->err($e->getCode().' - '.$e->getMessage());
      return $this->renderJson(array(
        'confirmed' => false,
      ));          
    }
    
  }
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {              
    try {
      $response = trim(file_get_contents('php://input'));

      $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getLogFilename()));
      $logger->log('Response: '.$response);

      $data = json_decode($response, true);

      $table = TaskTable::getInstance();

      if (!empty($data))
      {
        $taskType = 'project.'.$data['action'];

        $coreId = !empty($data['id']) ? $data['id'] : false;
        if ($coreId)
        {
          if ($task = $table->getByCoreId($coreId))
          {
            $task->setContentData($data);
            $task->core_packet_id = isset($data['packet_id']) ? $data['packet_id'] : null;
            $task->trySave();
          }
        }
        else if (!empty($data['action']))
        {
          $packetId = !empty($data['packet_id']) ? $data['packet_id']: false;
          if ($packetId)
          {
            $range = array($packetId);

            $maxPacketId = $table->getMaxCorePacketId($taskType);
            if ($packetId > $maxPacketId)
            {
              $range = range($maxPacketId + 1, $packetId);
            }

            foreach ($range as $i)
            {
              $task = new Task();
              $task->fromArray(array(
                'type'           => $taskType,
                'core_packet_id' => $i,
              ));
              $data['packet_id'] = $i;
              $task->setContentData($data);
              $task->trySave();
            }
          }
          else {
            $task = new Task();
            $task->fromArray(array(
              'type'           => $taskType,
            ));
            $task->setContentData($data);
            $task->trySave();
          }
        }
      }

      return $this->renderJson(array(
        'confirmed' => true,
      ));
    }
    catch (Exception $e) {
      $logger->err($e->getCode().' - '.$e->getMessage());
    }


    return sfView::NONE;
  }

 /**
  * Executes log action
  *
  * @param sfRequest $request A request object
  */
  public function executeLog(sfWebRequest $request)
  {
    $count = $request->getParameter('count', 40);
    $file = $this->getLogFilename();

    $this->content = is_readable($file) ? shell_exec("tail -n {$count} {$file}") : false;
   }



   protected function getLogFilename()
   {
     return sfConfig::get('sf_log_dir').'/api.log';
   }
}
