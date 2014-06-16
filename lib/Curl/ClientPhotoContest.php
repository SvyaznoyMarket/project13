<?php
namespace Curl;

class ClientPhotoContest extends Client {
	
	
	/**
	 * Добавляем проброску параметров запрос от клиента
	 * @ignore передавать эти данные через POST при необходимости между слоями более "карявое" и геморное решение ИМХО
     * @param string     $url
     * @param array      $data
     * @param float|null $timeout
     * @return resource
     */
	protected function create($url, array $data = [], $timeout = null) {
		$connection = parent::create($url, $data, $timeout);
		
		// Добавляем проброску заголовков
		if(isset($_SERVER['HTTP_REFERER'])) {
			curl_setopt($connection, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
		}
		
		curl_setopt($connection, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
//		curl_setopt($connection, CURLOPT_COOKIE, gzencode($_SERVER['HTTP_COOKIE'],5));
		curl_setopt($connection, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
		
		return $connection;
	}
	
	
	/**
	 * Переопределим метод декодирования, для того что бы в response отдавалось то что передано, а не массив
	 * 
     * @param string $response Тело ответа без заголовка (header)
     * @throws \RuntimeException
     * @throws Exception
     * @return mixed
     */
    protected function decode($response) {
        if (is_null($response)) {
            throw new \RuntimeException('Пустой ответ');
        }
		
        $decoded = (array)json_decode($response);
        if ($code = json_last_error()) {
            switch ($code) {
                case JSON_ERROR_DEPTH:
                    $message = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $message = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $message = 'Unknown error';
                    break;
            }
			
            throw new \RuntimeException($message, $code);
        }
		
        if (!empty($decoded)) {
            if (array_key_exists('error', $decoded)) {
				$decoded['error'] = (array)$decoded['error'];
                $e = new Exception(
					((isset($decoded['error']['message']) && is_scalar($decoded['error']['message'])) 
						? $decoded['error']['message'] 
						: 'В ответе содержится ошибка'), 
					(int)$decoded['error']['code']
				);

                /**
                 * $e->setContent нужен для того, чтобы сохранять ошибки от /v2/order/calc-tmp:
                 *   кроме error.code и error.message возвращается массив error.product_error_list
                 */
                $e->setContent($decoded['error']);
				
                throw $e;
            }

            if (array_key_exists('result', $decoded)) {
                $decoded = $decoded['result'];
            }
        }
		

        return $decoded;
    }
	
}

