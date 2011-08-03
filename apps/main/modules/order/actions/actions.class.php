<?php

/**
 * order actions.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class orderActions extends sfActions
{
  const LAST_STEP = 3;

  /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->order = $this->getRoute()->getObject();
  }
 /**
  * Executes new action
  *
  * @param sfRequest $request A request object
  */
  public function executeNew(sfWebRequest $request)
  {

  }
 /**
  * Executes step action
  *
  * @param sfRequest $request A request object
  */
  public function executeStep(sfWebRequest $request)
  {
    $this->step = $request['step'];
    $class = sfInflector::camelize("order_step_{$this->step}_form");
    $this->forward404Unless(!empty($this->step) && class_exists($class), 'Invalid order step');

    $form = new $class($this->getUser()->getOrder()->getForm($this->step));
    if ($request->isMethod('post'))
    {
      if ($form->isValid())
      {
        $this->getUser()->getOrder()->setForm($this->step, $form);

        if (self::LAST_STEP == $this->step)
        {
          $this->redirect('order_create');
        }
        else {
          $this->redirect('order_step', array('step' => $this->step + 1));
        }
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

  }
}
