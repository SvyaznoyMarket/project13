<?php

/**
 * productComment actions.
 *
 * @package    enter
 * @subpackage productComment
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCommentActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser()));
  }
 /**
  * Executes new action
  *
  * @param sfRequest $request A request object
  */
  public function executeNew(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser()));

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $this->form->save();

        $this->redirect(array('sf_route' => 'productComment', 'sf_subject' => $this->product));
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->setTemplate('index');
  }
}
