<?php

/**
 * smartengine actions.
 *
 * @package    enter
 * @subpackage smartengine
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class smartengineActions extends myActions
{
  /**
   * Executes also viewed action
   *
   * @param sfRequest $request A request object
   */
  public function executeAlsoViewed(sfWebRequest $request)
  {
    try {
      $product = RepositoryManager::getProduct()->getById($request['product'], true);
      $this->forward404If(!$product);

      $client = SmartengineClient::getInstance();

      $params = array(
        'sessionid'         => session_id(),
        'itemid'            => $product->getId(),
      );
      if ($this->getUser()->isAuthenticated()) {
        $params['userid'] = $this->getUser()->getGuardUser()->getId();
      }
      if ($product->getMainCategory()) {
        $params['itemtype'] = $product->getMainCategory()->getId();
        $params['requesteditemtype'] = $product->getMainCategory()->getId();
      }
      $r = $client->query('otherusersalsoviewed', $params);

      if (isset($r['error'])) {
        $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

        return $this->renderText('');
      }

      $ids =
        array_key_exists('id', $r['recommendeditems']['item'])
          ? array($r['recommendeditems']['item']['id'])
          : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : array());
      if (!count($ids)) {
        return $this->renderText('');
      }

      $products = RepositoryManager::getProduct()->getListById($ids, true);
      foreach ($products as $i => $product) {
        if (!$product->getIsBuyable()) unset($products[$i]);
      }
      if (!count($products)) {
        return $this->renderText('');
      }

      return $this->renderPartial($this->getModuleName().'/product_list', array(
        'title'    => 'С этим товаром также смотрят',
        'products' => $products,
      ));
    } catch(Exception $e) {
      return $this->renderText('');
    }
  }

 /**
  * Executes also bought action
  *
  * @param sfRequest $request A request object
  */
  public function executeAlsoBought(sfWebRequest $request)
  {
    try {
      $product = RepositoryManager::getProduct()->getById($request['product'], true);
      $this->forward404If(!$product);

      $client = SmartengineClient::getInstance();

      $params = array(
        'sessionid' => session_id(),
        'itemid'    => $product->getId(),
      );
      if ($this->getUser()->isAuthenticated()) {
        $params['userid'] = $this->getUser()->getGuardUser()->getId();
      }
      if ($product->getMainCategory()) {
        $params['itemtype'] = $product->getMainCategory()->getId();
        $params['requesteditemtype'] = $product->getMainCategory()->getId();
      }
      $r = $client->query('otherusersalsobought', $params);

      if (isset($r['error'])) {
        $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

        return $this->renderText('');
      }

      $ids =
        array_key_exists('id', $r['recommendeditems']['item'])
          ? array($r['recommendeditems']['item']['id'])
          : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : array());
      if (!count($ids)) {
        return $this->renderText('');
      }

      $products = RepositoryManager::getProduct()->getListById($ids, true);
      foreach ($products as $i => $product) {
        if (!$product->getIsBuyable()) unset($products[$i]);
      }
      if (!count($products)) {
        return $this->renderText('');
      }

      return $this->renderPartial($this->getModuleName().'/product_list', array(
        'title'    => 'Also bought',
        'products' => $products,
      ));
    } catch(Exception $e) {
      return $this->renderText('');
    }
  }

  /**
   * Executes also bought action
   *
   * @param sfRequest $request A request object
   */
  public function executeUserRecommendation(sfWebRequest $request)
  {
    try {
      if (!$this->getUser()->isAuthenticated()) {
        return $this->renderText('');
      }

      $product = RepositoryManager::getProduct()->getById($request['product'], true);
      $this->forward404If(!$product);

      $client = SmartengineClient::getInstance();
      $r = $client->query('recommendationsforuser', array(
        'sessionid'  => session_id(),
        'userid'     => $this->getUser()->getGuardUser()->core_id, // $this->getUser()->getGuardUser()->getId()
        'actiontype' => 'BUY',
      ));

      if (isset($r['error'])) {
        $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

        return $this->renderText('');
      }

      $ids = array_map(function($item) { return $item['id']; }, $r['recommendeditems']['item']);
      $products = RepositoryManager::getProduct()->getListById($ids, true);

      return $this->renderPartial($this->getModuleName().'/alsoBought', array(
        'products' => $products,
      ));
    } catch(Exception $e) {
      return $this->renderText('');
    }
  }
}
