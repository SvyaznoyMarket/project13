<?php

/**
 * cart actions.
 *
 * @package    enter
 * @subpackage callback
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

/**
 * @property $form CallbackForm
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
    $this->getResponse()->setTitle('Обратная связь – Enter.ru');

    $this->setVar('currentPage', 'callback', true);

    $this->form = new CallbackForm();
  }

    public function executeSend(sfWebRequest $request)
    {
        $this->getResponse()->setTitle('Обратная связь – Enter.ru');

        $this->setVar('currentPage', 'callback', true);

        $this->form = new CallbackForm();
        $data = $request->getParameter($this->form->getName());


        #$user = $this->getUser();
        #if (isset($user) && $user->getGuardUser() && $user->isAuthenticated()) $userId = $user->getGuardUser()->id;
        #else $userId = 0;


        $data['channel_id'] = 1;
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

    public function executeIpadSave(sfWebRequest $request)
    {
        $name = $request->getParameter('name');
        $email = $request->getParameter('email');
        $phone = $request->getParameter('phone');
        $product = $request->getParameter('product');

//        $name = 'aaaa';
//        $email = 'aaaa';
//        $phone = 'aaaa';
//        $product = 'aaaa';

        $result = array();
        $result['error'] = '';
        if (!$name) {
            $result['error']['name'] = 'Пожалуйста, укажите Ваше имя';
            $result['result'] = 'error';
        }
        if (!$email) {
            $result['error']['email'] = 'Пожалуйста, укажите Ваш email';
            $result['result'] = 'error';
        }
        if (!$phone) {
            $result['error']['phone'] = 'Пожалуйста, укажите Ваш телефон';
            $result['result'] = 'error';
        }
        if (!$product) {
            $result['error']['phone'] = 'Не указан продукт, который Вы хотите зарезервировать.';
            $result['result'] = 'error';
        }

//      Планшетный компьютер Apple Новый iPad WiFi + Cellular 64 ГБ белый

        $map = array(
          'Планшетный компьютер Apple Новый iPad WiFi 16 ГБ черный' => '457-0745',
          'Планшетный компьютер Apple Новый iPad WiFi 16 ГБ белый' => '457-0751',
          'Планшетный компьютер Apple Новый iPad WiFi 32 ГБ черный' => '457-0746',
          'Планшетный компьютер Apple Новый iPad WiFi 32 ГБ белый' => '457-0752',
          'Планшетный компьютер Apple Новый iPad WiFi 64 ГБ черный' => '457-0750',
          'Планшетный компьютер Apple Новый iPad WiFi 64 ГБ белый' => '457-0753',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 16 ГБ черный' => '457-0747',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 16 ГБ белый' => '457-0754',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 32 ГБ черный' => '457-0748',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 32 ГБ белый' => '457-0755',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 64 ГБ черный' => '457-0749',
          'Планшетный компьютер Apple Новый iPad WiFi + Cellular 64 ГБ белый' => '457-0756'
        );

        if (empty($result['error'])) {
            $new = IpadActionTable::getInstance()->create(array(
                'email' => $email,
                'phone' => $phone,
                'name' => $name,
                'product' => $product,
                'added' => date('Y-m-d H:i:s'),
                'article' => (isset($map[$product])?$map[$product] : NULL),
                'region' => sfContext::getInstance()->getUser()->getRegionCoreId(),
            ));
            $new->save();
            //myDebug::dump($comment);
            $result['result'] = 'ok';
        }

        if ($request->isXmlHttpRequest()) {
            $return = array(
                'success' => $result['result'],
                'data' => array(
                    'error' => $result['error']
                )
            );

            return $this->renderJson($return);
        }
        $this->redirect($this->getRequest()->getReferer());
        //return $result;
    }

}
