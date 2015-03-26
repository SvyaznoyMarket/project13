<?php

namespace Controller\Photocontest;

use Controller\Error\NotFoundAction;
use Http\Response;

class PhotoAction {
	
	public function show(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();

        try {
		    $contest = $curl->query('contest/item/'.$request->get('contestRoute'));
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            return (new NotFoundAction())->execute($e,$request);
        }
		
		$page = new \View\Photocontest\PhotoPage();
		$page->setParam('breadcrumbs', [
			[
				'name'	=> 'Главная',
				'url'	=> '/',
			],[
				'name'	=> 'Конкурс',
				'url'	=> \App::router()->generate('pc.homepage'),
			],[
				'name'	=> $contest->name,
				'url'	=> \App::router()->generate('pc.contest',['contestRoute'=>$request->get('contestRoute')]),
			],
		]);
		
		$page->setParam('contest',$contest);

        try {
            $page->setParam('item',
                $curl->query('image/item',['id'=>$request->get('id')])
            );
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            return (new NotFoundAction())->execute($e,$request);
        }

		$page->setParam('list',
			$curl->query('image/list/'.$request->get('contestRoute'),['limit'=>100])
		);
		
		return new \Http\Response($page->show());
	}
	
	
	public function create(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		
		$hasError= false;
		$form	= (object)[
//			'name'		=> (object)['title'=>'Заголовок'],
//			'orderIds'	=> (object)['title'=>'Номер(а) заказа'],
			'file'		=> (object)['title'=>'Фото'],
			'email'		=> (object)['title'=>'E-mail'],
			'mobile'	=> (object)['title'=>'Мобильный телефон'],
			'isAccept'	=> (object)['title'=>null,'value'=>1],
		];
		
		$user	= \App::user()->getEntity();
		// Если нет пользователя кидаем на форму авторизации
		if(!$user) {
			return new \Http\RedirectResponse(
				\App::router()->generate('user.login', ['redirect_to'=>rawurlencode($request->getRequestUri())])
			);
		}
		if($user->getEmail()) {
			unset($form->email);
		}
		if($user->getMobilePhone()) {
			unset($form->mobile);
		}
		
		$curl = \App::photoContestClient();
		
		$contest = $curl->query('contest/item/'.$request->get('contestRoute'));
		//@todo Если нет то прокидываем 404
		
		$page = new \View\Photocontest\PhotoCreatePage();
		$page->setParam('breadcrumbs', [
			[
				'name'	=> 'Главная',
				'url'	=> '/',
			],[
				'name'	=> 'Конкурс',
				'url'	=> \App::router()->generate('pc.homepage'),
			],[
				'name'	=> $contest->name,
				'url'	=> \App::router()->generate('pc.contest',['contestRoute'=>$contest->route]),
			],
		]);
		
		
		/**
		 * Дико извиняюсь, но дальше лестница, не понимаю как это по человечески тут закодить и времени нет
		 * кондовенько чтоб работало
		 */
		if($request->isMethod('POST')) {
			// активируем стандартный POST запрос
			$curl->getCurl()->setNativePost();
			
			// записываем имеющиеся значения в форму
			if(isset($form->name))		$form->name->value		= $request->get('name');
			if(isset($form->orderIds))	$form->orderIds->value	= $request->get('orderIds');
			if(isset($form->email))		$form->email->value		= $request->get('email');
			if(isset($form->mobile))	$form->mobile->value	= $request->get('mobile');
			if(isset($form->isAccept))	$form->isAccept->value	= $request->get('isAccept');
			
			
			// Если не хватает контакта у пользователя, то валидируем и добавляем
			try {
				$update = [];
				if(isset($form->email) && !$request->get('email')) {
					$hasError = true;
					$form->email->error = 'Необходимо указать email';
				} elseif(isset($form->email)) {
					$update['email'] = $request->get('email');
				}
				
				if(isset($form->mobile) && !$request->get('mobile')) {
					$hasError = true;
					$form->mobile->error = 'Необходимо указать мобильный телефон';
				} elseif(isset($form->mobile)) {
					$update['mobile'] = $request->get('mobile');
				}
				
				if(!empty($update))
					\App::coreClientV2()->query('user/update', ['token' => \App::user()->getToken()], $update, \App::config()->coreV2['hugeTimeout']);
				
			} catch (\Curl\Exception $e) {
				\App::exception()->remove($e);
				if($e->getCode()) {
					$t = $e->getContent();
					foreach($t['detail'] as $k =>$v) {
						$hasError = true;
						if(isset($form->$k))
							$form->$k->error = $v[0]['message'];
					}
				}
			}
			
			// грузим изображение
			try {
				if(isset($form->name) && !$request->get('name')) {
					$hasError = true;
					$form->name->error = 'Необходимо указать заголовок';
				}
				
				if(isset($form->orderIds) && !$request->get('orderIds')) {
					$hasError = true;
					$form->orderIds->error = 'Необходимо указать номер(а) Ваших заказов';
				}
				
				if(isset($form->isAccept) && !$request->get('isAccept')) {
					$hasError = true;
					$form->isAccept->error = 'Примите условия участия';
				}
				
				
				if(!$_FILES['file']['name']) {
					$hasError = true;
					$form->file->error = 'Вы не прикрепили файл';
				} elseif($_FILES['file']['error']===1) {
					$hasError = true;
					$form->file->error = 'Размер файла не может превышать '.str_replace('M','Мб',ini_get('upload_max_filesize'));
				} elseif($_FILES['file']['error']>1) {
					$hasError = true;
					$form->file->error = 'Не удается загрузить файл';
				} elseif(
					!in_array (
						mime_content_type($_FILES['file']['tmp_name']),
						['image/jpeg','image/gif','image/png']
					)
				) {
					$hasError = true;
					$form->file->error = 'Некорректный тип файла';
				}
				
				if(
					!$hasError 
					&& ($r = $curl->query(
						'image/create/'.$contest->id, [], [
							'name'		=> $request->get('name'),
							'orderIds'	=> $request->get('orderIds'),
							'file'		=> '@'.$_FILES['file']['tmp_name']
										.';filename='.$_FILES['file']['name']
										.';type='.$_FILES['file']['type']
						], 6
					))
				) {
					$page->setParam('message', 'Вы стали участником фотоконкурса. Ваше фото на модерации и появится в течение 24 часов');
					unset($form);	// отключаем вывод формы
				}
				
			} catch (\Exception $e) {
				\App::exception()->remove($e);
				$page->setParam('message', 'К сожалению что-то пошло не так, попробуйте загрузить фото позднее.');
			}
		}
		
		
		if(isset($form)) {
			$page->setParam('form', $form);
		}
		
		return new \Http\Response($page->show());
	}
	
	
	/**
	 * Кручу верчу запутать хочу )))
	 * внимательно смотрим код
	 * 
	 * Суть логики:
	 * Виджет голосования при вызове спрашивает сервер безопасный ключ, который актуален в течении 1/2-х минут
	 * Ключ кладется в заголовок x-page-id, т.к. он присутствует во всех ответах сервера (правда ключ длиннее обычного заголовка)
	 * Так же при ответе в JSON отдается неправильный ключ 
	 * Он используется в последствии в качестве очевидного GET параметра, 
	 * но реальный ключ отдается в заголовке X-referer вместе с реальным значением Referer после чего достается и используется в работе с бэкэндом
	 * 
	 * Броузерного бота конечно не сломает, а вот человеку пишущему серверного бота, как минимум, нервы попортит изрядно, при условии опфускации JS кода
	 */
	
	public function safeKey(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$r = $curl->query('vote/safeKey');
		
		/**
		 * Honeypot на дурочка
		 */
		setcookie(md5('123sk456'),$r->fakeKey);	// кручу верчу ))
		return new \Http\JsonResponse(
			array(
				'result' => $r->fakeKey
			),
			200,
			['x-page-id' => $r->safeKey] // кладем настоящий ключ сюда
		);
	}
	
	
	public function vote(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		return $this->voteRequest($request,'create');
	}
	
	
	
	public function unvote(\Http\Request $request) {
		//\App::logger()->debug('Exec ' . __METHOD__);
		return $this->voteRequest($request,'delete');
	}
	
	
	/**
	 * 
	 * @param \Http\Request $request
	 * @param string $action
	 * @return \Http\JsonResponse
	 */
	protected function voteRequest(\Http\Request $request,$action='create'){
		
		$curl = \App::photoContestClient();
		try {
			// тут берем настоящий ключ
			$safeKey = explode(' ',$request->headers->get('X-Referer'))[1];
			$r = $curl->query('vote/'.$action.'/'.$request->get('id'),[
				'safeKey'	=> $safeKey
			]);
			return new \Http\JsonResponse(['result'=>$r]);
		} catch (\Exception $e) {
			\App::exception()->remove($e);
			return new \Http\JsonResponse([
				'error'	=> [
					'message'	=> $e->getMessage(),
					'code'		=> $e->getCode()
				]
			]);
		}
	}
}
