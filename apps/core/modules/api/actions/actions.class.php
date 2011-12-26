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
   * Сбрасывает кеш списка сущностей.
   * Принимает массив. Для каждой сущности передаётся:
   *  - type = [product, product_category]
   *  - id - id сущности
   * 
   * @param sfWebRequest $request
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
      #$data[] = array('type' => 'product', 'id' => 10);
      #print_r($data);
      $productTable = ProductTable::getInstance();
      foreach($data as $item) {
          if (!isset($item['type']) || !isset($item['id'])) {
              continue;
          }
          if ($item['type'] == 'product') {
              $product = $productTable->findOneBy('core_id', $item['id']);
              if (!$product) {
                  $logger->log('Попытка очистить кеш не существующего товара id='.$product['id']);
                  continue;
              }
              #myDebug::dump($product);
              //очищаем кеш сущности
              CacheEraser::getInstance()->erase($product->getTable()->getCacheEraserKeys($product, 'show'));          
          } 
      }
      
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
   * Возвращает статус списка пакетов синхранизации.
   * Принимает массив id пакетов.
   * Возвращает массив. Элемент массива имеет вид:
   *    id пакета => [0 | 1]
   * 
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
      
      #$data['list'] = array(1881,1882,1883);
      #$data['id'] = 85318;
      if (!isset($data['list']) || !count($data['list'])) {
          $logger->err('Ошибка. В запросе статуса пакетов не передан список id пакетов.');          
          return $this->renderJson(array(
            'confirmed' => false,
          ));          
      }
      #print_r($data);
      $table = TaskTable::getInstance();
      $packetList = $table->getQueryObject()
              ->whereIn('core_packet_id', $data['list'])
              ->fetchArray()
            ;
      #print_r($packetList);
      $result = array();
      foreach($packetList as $packetInfo) {
          if (isset($packetInfo) && isset($packetInfo['status'])) {
              if ($packetInfo['status'] == 'success') {
                  $result[$packetInfo['core_packet_id']] = 1;
              } elseif ($packetInfo['status'] == 'fail') {
                  $result[$packetInfo['core_packet_id']] = 2;
              } else {
                  $result[$packetInfo['core_packet_id']] = 0;
              }
          } else {
              $result[$packetInfo['core_packet_id']] = 0;          
          }
      }
      #print_r($result);
      return $this->renderJson(array(
        'confirmed' => true,
        'status' => $result  
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
