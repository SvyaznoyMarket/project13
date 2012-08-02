<?php

/**
 * wordpress actions.
 *
 * @package    enter
 * @subpackage wordpress
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class wordpressActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      $page = $request->getParameter('page', 'about_company');

      $wpRequest = new WPRequest();
      $wpRequest->setUrl(sfConfig::get('app_wp_url'));
      $wpRequest->setMethod(WPRequest::methodPost);
      $wpRequest->setParameterList(array('json' => True));
      $wpResponse = $wpRequest->send($page);
      #@TODO: test it!
      $this->forward404If(!$wpResponse);

      $this->wpContent = $wpResponse['content'];
      $this->wpTitle = $wpResponse['title'];
      $this->getResponse()->setTitle($wpResponse['title']);
      $this->getRequest()->setParameter('_template', 'infopage');
      if(!empty($wpResponse['layout']))
      {
          $this->setLayout($wpResponse['layout']);
      }
  }
}
