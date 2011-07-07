<?php

/**
 * userTag actions.
 *
 * @package    enter
 * @subpackage userTag
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userTagActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->userTagList = $this->getUser()->getGuardUser()->getTagList();
  }
 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete(sfWebRequest $request)
  {
    $userTag = $this->getRoute()->getObject();

    UserTagTable::getInstance()->createQuery()
      ->delete()
      ->where('id = ?', $userTag->id)
      ->execute()
    ;

    $this->redirect($this->getRequest()->getReferer());
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new UserTagForm();

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $this->form->getObject()->user_id = $this->getUser()->getGuardUser()->id;
        $this->form->save();
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->redirect('userTag');
  }
 /**
  * Executes link product action
  *
  * @param sfRequest $request A request object
  */
  public function executeLinkProduct(sfWebRequest $request)
  {
    $userTag = $this->getRoute()->getObject();

    $this->forward404Unless($product = ProductTable::getInstance()->getByToken($request['product']));

    $userTagProductRelation = new UserTagProductRelation();
    $userTagProductRelation->fromArray(array(
      'tag_id'     => $userTag->id,
      'product_id' => $product->id,
    ));
    $userTagProductRelation->replace();

    $this->redirect($request->getReferer());
  }
 /**
  * Executes unlink product action
  *
  * @param sfRequest $request A request object
  */
  public function executeUnlinkProduct(sfWebRequest $request)
  {
    $userTag = $this->getRoute()->getObject();

    $this->forward404Unless($product = ProductTable::getInstance()->getByToken($request['product']));

    UserTagProductRelationTable::getInstance()->createQuery()
      ->delete()
      ->where('tag_id = ? AND product_id = ?', array($userTag->id, $product->id))
      ->execute()
    ;

    $this->redirect($request->getReferer());
  }
}
