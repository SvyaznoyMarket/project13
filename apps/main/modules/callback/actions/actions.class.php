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
        $data['name'] = trim($data['name']);
        $data['email'] = trim($data['email']);
        $data['theme'] = trim($data['theme']);
        $data['text'] = trim($data['text']);
        //$data['categoty_id'] = 21;
        $this->form->bind($data);
        $this->setTemplate('index');
        $this->setVar('error', '', true);                          
        

        if ($this->form->isValid())
        {
            try
            {
               // $this->form->getObject()->setCorePush(false);
                $result = $this->form->save();
                if (!$result) $this->setVar('error', 'К сожалению, отправить форму не удалось.', true);                          

                /*
                if ($result){
                    //отправляем письмо администратору
                    
                    $letterBody = "
                    Обратная связь на сайте Enter.ru. <br><br>
                    Содержание сообщение:<br>
                    Имя: ".$data['name']."<br>
                    E-mail: ".$data['email']."<br>
                    Тема вопроса: ".$data['theme']."<br>
                    Текст вопроса:  ".$data['text']."<br>
                    ";
                    $mailer = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
                    $message = Swift_Message::newInstance( $data['theme'] )
                             ->setFrom(array($data['email'] => $data['name']))
                             ->setTo(array('esbelousova@maxus.ru' => 'site admin'))
                             ->setBody($letterBody, 'text/html');
                    $res = $mailer->send($message);   
                    #var_dump($res);
                    $message = Swift_Message::newInstance( $data['theme'] )
                             ->setFrom(array($data['email'] => $data['name']))
                             ->setTo(array('olga--tru@yandex.ru' => 'site admin'))
                             ->setBody($letterBody, 'text/html');
                    $res = $mailer->send($message);   
                    #var_dump($res);

//}
                }          else{
                    $this->setVar('error', 'К сожалению, отправить форму не удалось.', true);                          
                }
                 * 
                 */
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
