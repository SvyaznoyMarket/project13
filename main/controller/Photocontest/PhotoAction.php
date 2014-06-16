<?php

namespace Controller\Photocontest;

class PhotoAction {
	
	public function show(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$contest = $curl->query('contest/'.$request->get('contestId'));
		
		$page = new \View\Photocontest\PhotoPage();
		
		$page->setParam('breadcrumbs', [
			array(
				'name'	=> $contest->name,
				'url'	=> \App::router()->generate('pc.contest',['id'=>$request->get('contestId')]),
			),
		]);
		
		$page->setParam('item',
			$curl->query('image/item',['id'=>$request->get('id')])
		);
		
		$page->setParam('list',
			$curl->query('image/list/'.$request->get('contestId'),['limit'=>100])
		);
		
		return new \Http\Response($page->show());
	}
	
	
	/**
	 * Кручу верчу запутать хочу )))
	 * читаем коменты, внимательно смотрим код
	 */
	
	
	public function vote(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		return $this->voteRequest($request,'create');
	}
	
	
	
	public function unvote(\Http\Request $request) {
		\App::logger()->debug('Exec ' . __METHOD__);
		return $this->voteRequest($request,'delete');
	}
	
	
	public function safeKey(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$r = $curl->query('vote/safeKey');
		
		/**
		 * Honeypot на дурочка
		 */
		setcookie(md5('123sk456'),$r->fakeKey);
		return new \Http\Response(
			json_encode(array(
				'result' => $r->fakeKey
			)),
			200,
			['x-page-id' => $r->safeKey] // кладем настоящий ключ сюда
		);
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
