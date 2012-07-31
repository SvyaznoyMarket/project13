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
      $sfWPProxy = SF_WP_Proxy::getInstance();
      $this->wpContent = $sfWPProxy->getContent();

      $this->forward404If(!$sfWPProxy->getPost());

      $breadCrumbElementList = array();
      $currentPage = $sfWPProxy->getPost();
      while(!empty($currentPage->post_parent))
      {
          $parentPage = $sfWPProxy->getPage($currentPage->post_parent);
          array_unshift($breadCrumbElementList, array('name' => $parentPage->post_title, 'url' => $sfWPProxy->getPermalink($parentPage->ID)));
          $currentPage = $parentPage;
      }

      $this->pageTitle = $sfWPProxy->getTitle('', '', False);
      $breadCrumbElementList[] = array('name' => $this->pageTitle);
      $this->setVar('breadCrumbElementList', $breadCrumbElementList);

      $this->getRequest()->setParameter('_template', 'infopage');
      $this->getResponse()->setTitle($this->pageTitle);

      $layout = $sfWPProxy->getCurrentLayout();

      if(!empty($layout))
      {
          $this->setLayout($layout);
      }
  }
}
