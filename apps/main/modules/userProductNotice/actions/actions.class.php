<?php

/**
 * userProductNotice actions.
 *
 * @package    enter
 * @subpackage userProductNotice
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductNoticeActions extends sfActions
{
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
    $this->product = $this->getRoute()->getObject();
    $this->form = new UserProductNoticeForm();

    if ($this->getUser()->isAuthenticated())
    {
      $this->form->setDefaults(array(
        'type'  => UserProductNoticeTable::getInstance()->getListByUser($this->getUser()->getGuarduser()->id)->toValueArray('type'),
        'email' => $this->getUser()->getGuardUser()->email,
      ));
    }
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
    $this->form = new UserProductNoticeForm();

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        foreach (UserProductNoticeTable::getInstance()->getEnumValues('type') as $type)
        {
          if (in_array($type, $this->form->getValue('type')))
          {
            $userProductNotice = new UserProductNotice();
            $userProductNotice->fromArray(array(
              'type'       => $type,
              'product_id' => $this->product,
              'user_id'    => $this->getUser()->isAuthenticated() ? $this->getUser()->getGuarduser()->id : null,
              'email'      => $this->form->getValue('email'),
            ));

            $userProductNotice->replace();
          }
          else if ($this->getUser()->isAuthenticated()) {
            UserProductNoticeTable::getInstance()->createQuery()
              ->delete()
              ->where('type = ? AND product_id = ? AND user_id = ?', array($type, $this->product->id, $this->getUser()->getGuarduser()->id))
              ->execute()
            ;
          }
        }

        $this->redirect(array('sf_route' => 'productCard', 'sf_subject' => $this->product));
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
      }
    }

    $this->setTemplate('show');
  }
}
