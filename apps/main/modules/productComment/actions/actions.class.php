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
  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'product_comment');
  }
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();

    $this->forward404If(!$this->product);
    $this->productEntity = RepositoryManager::getProduct()->getByid($this->product->core_id, true);

    $this->sort = $this->getRequestParameter('sort', 'created_desc');
    $this->page = $this->getRequestParameter('page', 1);

    // SEO ::
    $this->product->setDescription('<noindex>'.$this->product->getDescription().'</noindex>');
    $title = '%s - отзывы покупателей о товаре %s - интернет-магазин Enter.ru';
    $this->getResponse()->setTitle(sprintf(
      $title, $this->product->getName(), $this->product->getName()
    ));
    $descr = 'Интернет магазин Enter.ru предлагает ознакомиться с отзывами владельцев товара %s. На этой странице Вы можете прочитать отзывы покупателей о товаре %s, а так же оставить свое мнение.';
    $this->getResponse()->addMeta('description', sprintf(
      $descr, $this->product->getName(), $this->product->getName()
    ));
    $this->getResponse()->addMeta('keywords', sprintf('%s отзывы мнения покупателей владельцев пользователей', $this->product->getName()));

    /* SEO
	  $title = "«Отзывы»: ".$this->product['name'] . ' в магазинах "Enter" ';
    $mainCat = $this->product->getMainCategory();
    $title .= ' – '.$mainCat;
    if ($mainCat) {
      $rootCat = $mainCat->getRootRow();
      if ($rootCat->id !== $mainCat->id) {
        $title .= ' – '.$rootCat;
      }
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');
     *
     */

    $this->hasProductStockLink = false;
    try {
      $r = CoreClient::getInstance()->query('delivery/calc', array(
        'geo_id'       => $this->getUser()->getRegion('id'),
      ), array(
        'product_list' => array(array(
          'id'       => $this->product->core_id,
          'quantity' => 1,
        )),
      ));
    }
    catch (Exception $e) {
      $r = null;
    }

    if ($r && isset($r['product_list'][$this->product->core_id]['delivery_mode_list'])) {
      foreach ($r['product_list'][$this->product->core_id]['delivery_mode_list'] as $delivery) {
        if ('self' == $delivery['token']) {
          $this->hasProductStockLink = true;
          break;
        }
      }
    }
  }

  /**
   * Executes new action
   *
   * @param sfRequest $request A request object
   */
  public function executeNew(sfWebRequest $request)
  {
    //$this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $this->product = $this->getRoute()->getObject();

    if ($request->isMethod('post'))
    {
      if ($request->getParameter('content_resume') && $request->getParameter('rating'))
      {

        $userId = $this->getUser()->getGuardUser() ? $this->getUser()->getGuardUser()->getId() : null;

        $content = '';
        if ($request->getParameter('content_plus') != '')
        {
          $content .= 'Достоинства: <br/>'.$request->getParameter('content_plus').'<br/><br/>';
        }
        if ($request->getParameter('content_plus') != '')
        {
          $content .= 'Недостатки: <br/>'.$request->getParameter('content_minus').'<br/><br/>';
        }
        $content .= 'Резюме: <br/>'.$request->getParameter('content_resume');

        $comment = ProductCommentTable::getInstance()->create(array(
          'content' => $content,
          'user_id' => $userId,
          'rating' => $request->getParameter('rating'),
          'is_recomend' => $request->getParameter('is_recomend'),
        ));
        $comment->setProduct($this->product);
        $data = $comment->exportToCore();
        $data['user_id'] = $userId;
        $r = Core::getInstance()->query('product.opinion.create', array(), $data);

        $userRate = new UserProductRatingTotal();
        $userRate->fromArray(array('product_id' => $this->product->id, 'value' => $request->getParameter('rating')));
        $data = $userRate->exportToCore();
        $data['user_id'] = $userId;
        $r = Core::getInstance()->query('user.product.rating.create', array(), $data);

        $this->redirect(array('sf_route' => 'productComment', 'sf_subject' => $this->product));
      }
      else
      {

      }
    }
    $title = 'Новый отзыв о товаре "'.$this->product['name'].'"';
    $this->getResponse()->setTitle($title.' – Enter.ru');
  }

  /**
   * Executes create action
   *
   * @param sfRequest $request A request object
   */
  public function executeCreate(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $product = $this->getRoute()->getObject();

      $comment = ProductCommentTable::getInstance()->create(array(
        'content' => $request->getParameter('content'),
        //'user_id' => $this->getUser()->getGuardUser()->id,
      ));
      $comment->setProduct($product);
      $data = $comment->exportToCore();
      $data['user_id'] = $this->getUser()->getGuardUser() ? $this->getUser()->getGuardUser()->getId() : null;
      $r = Core::getInstance()->query('product.opinion.create', array(), $data);

      //$comment->setCorePush(false);
      //$comment->getNode()->insertAsLastChildOf(ProductCommentTable::getInstance()->getById($request->getParameter('parent_id')));

      $data = $comment->toArray(false);
      $data['user_name'] = strval($comment->getUser());
      $data['parent_id'] = $request->getParameter('parent_id');

      return $this->renderJson(array(
        'success' => true,
        'data' => $data,
      ));
    }
    else
    {
      return $this->renderJson(array(
        'success' => false,
      ));
    }
  }

  /**
   * Executes helpful action
   *
   * @param sfRequest $request A request object
   */
  public function executeHelpful(sfWebRequest $request)
  {
    $this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $product = ProductTable::getInstance()->getByToken($request['product']);
    $productComment = $this->getRoute()->getObject();

    $cookieName = sfConfig::get('app_product_comment_helpful_cookie', 'product_comment_helpful');
    $comments = explode('.', $this->getRequest()->getCookie($cookieName));

    if (true
      && ($productComment->user_id != $this->getUser()->getGuardUser()->id)
      && !in_array($productComment->id, $comments)
    )
    {
      if (in_array($request['helpful'], array('yes', 'true', 'on')))
      {
        $productComment->helpful++;
      }
      else
      {
        $productComment->unhelpful++;
      }
      $productComment->save();

      // сохранить ид комментария в куках
      $comments[] = $productComment->id;
      $this->getResponse()->setCookie($cookieName, implode('.', $comments), time() + 15 * 24 * 3600);
    }

    // response
    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'success' => true,
        'data' => array(
          'content' => $this->getComponent($this->getModuleName(), 'list', array('product' => $product)),
        ),
      ));
    }
    $this->redirect($request->getReferer());
  }

}
