<?php

class myValidatorCertificate extends sfValidatorBase
{
    protected function configure($options = array(), $messages = array())
    {
        $this->addMessage('invalid', 'Невалидный сертификат');
        $this->addMessage('error1', 'Сертификат заблокирован');
        $this->addMessage('error2', 'Сертификат не активирован');
        $this->addMessage('error3', 'Сертификат погашен');
        $this->addMessage('error4', 'Истек срок действия сертификата');

        parent::configure($options, $messages);
    }

    protected function doClean($value)
    {
        if (strlen(trim($value)) == 0) {
            return '';
        }

        try {
            $response = CoreClient::getInstance()->query('certificate/check', array('code' => $value));
            if (is_array($response) && array_key_exists('error', $response)) {
                $e = new CoreClientException($response['error']['message'], $response['error']['code']);
                throw $e;
            }

            $statusCode = (int)$response['status_code'];
            switch ($statusCode) {
                case 0:
                    return $value;
                    break;
                case 1:
                case 2:
                case 3:
                case 4:
                    throw new sfValidatorError($this, 'error' . $statusCode, array('value' => $value));
                    break;
            }
        } catch (CoreClientException $e) {
            if (-1 == $e->getCode()) {
                throw new sfValidatorError($this, 'invalid', array('value' => $value));
            }
        }

        return $value;
    }
}