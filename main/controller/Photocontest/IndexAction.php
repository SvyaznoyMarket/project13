<?php

namespace Controller\Photocontest;

class IndexAction {
	
	public function index(\Http\Request $request){
		\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$r = $curl->query('contest/lastActive');
		$request->query->set('id', $r->id);
		
		return $this->show($request);
	}
	
	
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function show(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

		$curl	= \App::photoContestClient();
		
        // подготовка 1-го пакета запросов
        // FIXME
		$contest = $curl->query('contest/'.$request->get('id'));
//        $curl->addQuery('contest/'.$request->get('id'), [], [],
//            function($result) use (&$contest) {
//			      не отдает данные гадина
//                $contest = $result;
//            }
//        );
//
//        // выполнение 1-го пакета запросов
//        $curl->execute();
		
        // теперь переменная $photos наполнена данными
		
        // страница
        $page = new \View\Photocontest\IndexPage();
        $page->setParam('contest', $contest);
		
		// спрашиваем топ
        $page->setParam('top', 
			$curl->query(
				'image/list/'.$request->get('id'),
				['order'=>'r','orderType'=>'d','limit'=>3]
			)
		);
		
		// спрашиваем страницу
        $page->setParam('list',
			$curl->query(
				'image/list/'.$request->get('id'),
				[
					'order'=>$request->get('order','d'),'orderType'=>'d',
					'limit'=>18,'page'=>$request->get('page',0)
				]
			)
		);
		
		$page->setParam('request', $request);

        return new \Http\Response($page->show());
    }
}
