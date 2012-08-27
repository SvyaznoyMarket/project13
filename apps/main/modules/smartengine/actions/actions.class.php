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
   * Executes view action
   *
   * @param sfRequest $request A request object
   */
  public function executeView(sfWebRequest $request)
  {
    $product = RepositoryManager::getProduct()->getById($request['product'], true);
    $this->forward404If(!$product);

    $client = SmartengineClient::getInstance();
    $params = array(
      'sessionid'       => session_id(),
      'itemid'          => $product->getId(),
      'itemtype'        => $product->getMainCategory()->getId(),
      'itemdescription' => $product->getName(),
      'itemurl'         => 'http://'.$request->getHost().$product->getLink(),
      'actiontime'      => date('d_m_Y_H_i_s'),
    );
    if ($this->getUser()->isAuthenticated()) {
      $params['userid'] = $this->getUser()->getGuardUser()->getId();
    }
    $r = $client->query('view', $params);

    if (isset($r['error'])) {
      $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);
    }

    return $this->renderText('');
  }

  /**
   * Executes view action
   *
   * @param sfRequest $request A request object
   */
  public function executeBuy(sfWebRequest $request)
  {
    $ids = explode('-', $request['product']);
    $products = count($ids) ? RepositoryManager::getProduct()->getListById($ids, true) : array();
    if (!count($products)) {
      return $this->renderText('');
    }

    $client = SmartengineClient::getInstance();

    foreach ($products as $product) {
      $params = array(
        'sessionid'       => session_id(),
        'itemid'          => $product->getId(),
        'itemtype'        => $product->getMainCategory()->getId(),
        'itemdescription' => $product->getName(),
        'itemurl'         => 'http://'.$request->getHost().$product->getLink(),
        'actiontime'      => date('d_m_Y_H_i_s'),
      );
      if ($this->getUser()->isAuthenticated()) {
        $params['userid'] = $this->getUser()->getGuardUser()->getId();
      }
      $r = $client->query('buy', $params);

      if (isset($r['error'])) {
        $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);
      }
    }

    return $this->renderText('');
  }

  /**
   * Executes also viewed action
   *
   * @param sfRequest $request A request object
   */
  public function executeAlsoViewed(sfWebRequest $request)
  {
    $product = RepositoryManager::getProduct()->getById($request['product'], true);
    $this->forward404If(!$product);

    $client = SmartengineClient::getInstance();

    $params = array(
      'sessionid' => session_id(),
      'itemid'    => $product->getId(),
      'itemtype'  => $product->getMainCategory()->getId(),
    );
    if ($this->getUser()->isAuthenticated()) {
      $params['userid'] = $this->getUser()->getGuardUser()->getId();
    }
    $r = $client->query('otherusersalsoviewed', $params);

    if (isset($r['error'])) {
      $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

      return $this->renderText('');
    }

    $ids = array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : array());
    if (!count($ids)) {
      return $this->renderText('');
    }

    $products = RepositoryManager::getProduct()->getListById($ids, true);

    return $this->renderPartial($this->getModuleName().'/product_list', array(
      'title'    => 'Also viewed',
      'products' => $products,
    ));
  }

 /**
  * Executes also bought action
  *
  * @param sfRequest $request A request object
  */
  public function executeAlsoBought(sfWebRequest $request)
  {
    $product = RepositoryManager::getProduct()->getById($request['product'], true);
    $this->forward404If(!$product);

    $client = SmartengineClient::getInstance();

    $params = array(
      'sessionid' => session_id(),
      'itemid'    => $product->getId(),
      'itemtype'  => $product->getMainCategory()->getId(),
    );
    if ($this->getUser()->isAuthenticated()) {
      $params['userid'] = $this->getUser()->getGuardUser()->getId();
    }
    $r = $client->query('otherusersalsobought', $params);

    if (isset($r['error'])) {
      $this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

      return $this->renderText('');
    }

    $ids = array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : array());
    if (!count($ids)) {
      return $this->renderText('');
    }

    $products = RepositoryManager::getProduct()->getListById($ids, true);

    return $this->renderPartial($this->getModuleName().'/product_list', array(
      'title'    => 'Also bought',
      'products' => $products,
    ));
  }

  /**
   * Executes also bought action
   *
   * @param sfRequest $request A request object
   */
  public function executeUserRecommendation(sfWebRequest $request)
  {
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
  }
}
