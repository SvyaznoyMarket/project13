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
    $this->parent = null;
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser()));
  }
 /**
  * Executes new action
  *
  * @param sfRequest $request A request object
  */
  public function executeNew(sfWebRequest $request)
  {
    $this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $this->product = $this->getRoute()->getObject();
    $this->parent =
      !empty($request['parent'])
      ? ProductCommentTable::getInstance()->getById($request['parent'])
      : null
    ;
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser(), 'parent' => $this->parent));
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $this->product = $this->getRoute()->getObject();
    $this->parent =
      !empty($request['parent'])
      ? ProductCommentTable::getInstance()->getById($request['parent'])
      : null
    ;
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser(), 'parent' => $this->parent));

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
 /**
  * Executes helpful action
  *
  * @param sfRequest $request A request object
  */
  public function executeHelpful(sfWebRequest $request)
  {
    $this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $productComment = $this->getRoute()->getObject();

    $cookieName = sfConfig::get('app_product_comment_helpful_cookie', 'product_comment_helpful');
    $comments = explode('.', $this->getRequest()->getCookie($cookieName));

    if (true
      &&($productComment->user_id != $this->getUser()->getGuardUser()->id)
      && !in_array($productComment->id, $comments)
    ) {
      if (in_array($request['helpful'], array('yes', 'true', 'on')))
      {
        $productComment->helpful++;
      }
      else {
        $productComment->unhelpful++;
      }
      $productComment->save();

      // сохранить ид комментария в куках
      $comments[] = $productComment->id;
      $this->getResponse()->setCookie($cookieName, implode('.', $comments), time() + 15 * 24 * 3600);
    }
    myDebug::dump($comments);

    $this->redirect($request->getReferer());
  }
}
