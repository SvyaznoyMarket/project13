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
    $this->page = (int)$request->getParameter('page', 1);
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
    
	if ($request->isMethod(sfWebRequest::POST)) {

		if ($request->getParameter('content_resume') && $request->getParameter('rating')) {
		
			$userId = 2;

			$content = '';
			if ($request->getParameter('content_plus') != '') {
				$content .= 'Достоинства: <br/>' . $request->getParameter('content_plus') . '<br/><br/>';
			}
			if ($request->getParameter('content_plus') != '') {
				$content .= 'Недостатки: <br/>' . $request->getParameter('content_minus') . '<br/><br/>';
			}
			$content .= 'Резюме: <br/>' . $request->getParameter('content_resume');

			$comment = ProductCommentTable::getInstance()->create(array(
				'content'     => $content,
				'user_id'     => $userId,
				'rating'      => $request->getParameter('rating'),
				'is_recomend' => $request->getParameter('is_recomend'),
			));
			$comment->setProduct($this->product);
			$comment->setCorePush(false);
			$comment->save();

			$ratings = $request->getParameter('rating_type');
			foreach ($ratings as $propertyId => $value) {
				$rateObj = UserProductRatingTable::getInstance()->create(array(
					'property_id' => $propertyId,
					'user_id' => $userId,
					'product_id' => $this->product->id,
					'value' => $value,
				));
				try {
				$rateObj->save();
				} catch (Exception $e) {}
			}

			$this->redirect(array('sf_route' => 'productComment', 'sf_subject' => $this->product));
		} else {
			
		}
	}
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
	  if (1==1 || $this->getUser()->isAuthenticated()) {
		  $product = $this->getRoute()->getObject();

		  $comment = ProductCommentTable::getInstance()->create(array(
			  'parent_id' => $request->getParameter('parent_id'),
			  'content'   => $request->getParameter('content'),
			  'user_id'   => 2
		  ));
		  $comment->setProduct($product);
		  $comment->setCorePush(false);
		  $comment->save();

		  $data = $comment->toArray(false);
		  $data['user_name'] = strval($comment->getUser());
		  return $this->renderJson(array(
			'success' => true,
			'data'    => $data,
		  ));
	  } else {
		  return $this->renderJson(array(
			'success' => false,
		  ));
	  }
	  
    $this->redirectUnless($this->getUser()->isAuthenticated(), '@user_signin');

    $this->product = $this->getRoute()->getObject();
    $this->parent =
      !empty($request['parent'])
      ? ProductCommentTable::getInstance()->getById($request['parent'])
      : null
    ;
    $this->form = new ProductCommentForm(array(), array('product' => $this->product, 'user' => $this->getUser()->getGuardUser(), 'parent' => $this->parent));

    $this->form->bind($request->getParameter($this->form->getName()));
    $this->form->updateObject();
    if ($this->form->isValid())
    {
      try
      {
        $this->form->save();

        // response
        if ($request->isXmlHttpRequest())
        {
          return $this->renderJson(array(
            'success' => true,
            'data'    => array(
              'element_id' => "product_{$this->product->id}_comment_{$this->form->getObject()->id}-block",
              'content'    => $this->getComponent($this->getModuleName(), 'form', array('product' => $this->product, 'parent' => $this->parent)),
              'list'       => $this->getComponent($this->getModuleName(), 'list', array('product' => $this->product)),
            ),
          ));
        }
        // response
        if ('frame' == $this->getLayout())
        {
          return $this->getPartial('default/close');
        }
        $this->redirect(array('sf_route' => 'productComment', 'sf_subject' => $this->product));
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    // response
    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'success' => false,
        'data'    => array(
          'content' => $this->getComponent($this->getModuleName(), 'form', array('product' => $this->product, 'parent' => $this->parent, 'form' => $this->form)),
        ),
      ));
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

    $product = ProductTable::getInstance()->getByToken($request['product']);
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

    // response
    if ($request->isXmlHttpRequest())
    {
      $this->getContext()->getConfiguration()->loadHelpers('Url');

      return $this->renderJson(array(
        'success' => true,
        'data'    => array(
          'content' => $this->getComponent($this->getModuleName(), 'list', array('product' => $product)),
        ),
      ));
    }
    $this->redirect($request->getReferer());
  }
}
