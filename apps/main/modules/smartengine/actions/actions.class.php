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
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeAlsoBought(sfWebRequest $request)
  {
    $product = RepositoryManager::getProduct()->getById($request['product'], true);
    $this->forward404If(!$product);

    $client = SmartengineClient::getInstance();
    $r = $client->query('otherusersalsobought', array(
      'sessionid' => session_id(),
      'itemid'    => $product->getId(),
      'itemtype'  => $product->getMainCategory()->getId(),
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
