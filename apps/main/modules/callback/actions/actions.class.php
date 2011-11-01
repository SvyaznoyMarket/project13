<?php

/**
 * cart actions.
 *
 * @package    enter
 * @subpackage callback
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class callbackActions extends myActions
{

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {

    $this->setVar('currentPage', 'callback', true);

    $this->form = new CallbackForm();


    #$cart = $this->getUser()->getCart();
    #$this->setVar('cart', $cart, true);
  }

    public function executeSend(sfWebRequest $request)
    {

        $this->setVar('currentPage', 'callback', true);

        $this->form = new CallbackForm();
        $data = $request->getParameter($this->form->getName());
        
        if (isset($data['name'])) $data['name'] = trim($data['name']);
        else $data['name'] = '';
        if (isset($data['email'])) $data['email'] = trim($data['email']);
        else $data['email'] = '';
        if (isset($data['theme'])) $data['theme'] = trim($data['theme']);
        else $data['theme'] = '';
        if (isset($data['text'])) $data['text'] = trim($data['text']);
        else $data['text'] = '';
        

        #$user = $this->getUser();
        #if (isset($user) && $user->getGuardUser() && $user->isAuthenticated()) $userId = $user->getGuardUser()->id;
        #else $userId = 0;


        $this->form->bind($data);
        $this->setTemplate('index');
        $this->setVar('error', '', true);

        if ($this->form->isValid())
        {
            try
            {
                #$this->form->getObject()->setCorePush(false);
                $this->form->save();
                $this->setTemplate('sendOk');
            }
            catch (Exception $e)
            {                
                //echo $e->getMessage();
                $this->setVar('error', 'К сожалению, отправить форму не удалось.', true);
                $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
                $this->setTemplate('index');
            }
        } else {
            //echo $this->form->renderGlobalErrors();
            //$this->redirect('callback');
            $this->setTemplate('index');
        }

    }

}
