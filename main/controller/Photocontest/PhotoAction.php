<?php

namespace Controller\Photocontest;

class PhotoAction {
	
	public function show(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$contest = $curl->query('contest/'.$request->get('contestId'));
		
		$page = new \View\Photocontest\PhotoPage();
		
		$page->setParam('breadcrumbs', [
			[
				'name'	=> 'Главная',
				'url'	=> '/',
			],[
				'name'	=> $contest->name,
				'url'	=> \App::router()->generate('pc.contest',['id'=>$request->get('contestId')]),
			],
		]);
		
		$page->setParam('contest',$contest);
		
		$page->setParam('item',
			$curl->query('image/item',['id'=>$request->get('id')])
		);
		
		$page->setParam('list',
			$curl->query('image/list/'.$request->get('contestId'),['limit'=>100])
		);
		
		return new \Http\Response($page->show());
	}
	
	
	public function create(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		
		$hasError= false;
		$form	= (object)[
			'title'		=> (object)['title'=>'Заголовок'],
			'orderIds'	=> (object)['title'=>'Номер(а) заказа'],
			'file'		=> (object)['title'=>'Фото'],
			'email'		=> (object)['title'=>'E-mail'],
			'mobile'	=> (object)['title'=>'Мобильный телефон'],
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
		
		$contest = $curl->query('contest/'.$request->get('contestId'));
		//@todo Если нет то прокидываем 404
		
		$page = new \View\Photocontest\PhotoCreatePage();
		$page->setParam('breadcrumbs', [
			[
				'name'	=> 'Главная',
				'url'	=> '/',
			],[
				'name'	=> $contest->name,
				'url'	=> \App::router()->generate('pc.contest',['id'=>$contest->id]),
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
			$form->title->value		= $request->get('title');
			$form->orderIds->value	= $request->get('orderIds');
			if(isset($form->email))
				$form->email->value	= $request->get('email');
			if(isset($form->mobile))
				$form->mobile->value= $request->get('mobile');
			
			
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
				if(!$request->get('title')) {
					$hasError = true;
					$form->title->error = 'Необходимо указать заголовок';
				}
				
				if(!$request->get('orderIds')) {
					$hasError = true;
					$form->orderIds->error = 'Необходимо указать номера Ваших заказов';
				}
				
				if(!$_FILES['file']['name']) {
					$hasError = true;
					$form->file->error = 'Необходимо указать загружаемый файл';
				} elseif($_FILES['file']['error']===1) {
					$hasError = true;
					$form->file->error = 'Файл слишком большой. Размер файла не может превышать '. ceil(ini_get('upload_max_filesize'));
				} elseif($_FILES['file']['error']>1) {
					$hasError = true;
					$form->file->error = 'Не удает загрузить файл.';
				}
				
				if(
					!$hasError 
					&& ($r = $curl->query(
						'image/create/'.$request->get('contestId'), [], 
						[
							'title'		=> $request->get('title'),
							'orderIds'	=> $request->get('orderIds'),
							'file'		=> '@'.$_FILES['file']['tmp_name']
										.';filename='.$_FILES['file']['name']
										.';type='.$_FILES['file']['type']
						]
					))
				) {
					$page->setParam('message', $r->message);
					return new \Http\Response($page->show());
				}
				
			} catch (Exception $e) {
				
			}
		}
		
		
		$page->setParam('form', $form);
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
		\App::logger()->debug('Exec ' . __METHOD__);
		
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
		\App::logger()->debug('Exec ' . __METHOD__);
		return $this->voteRequest($request,'create');
	}
	
	
	
	public function unvote(\Http\Request $request) {
		\App::logger()->debug('Exec ' . __METHOD__);
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
