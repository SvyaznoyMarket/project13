<?php

namespace Controller\Photocontest;

class IndexAction {
	
	public function index(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$r = $curl->query('contest/lastActive');
		$request->query->set('contestRoute', $r->route);
		
		return $this->contest($request);
	}
	
	
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function contest(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

		$curl	= \App::photoContestClient();
		$contest = $curl->query('contest/item/'.$request->get('contestRoute'));

        // страница
        $page = new \View\Photocontest\IndexPage();
        $page->setParam('contest', $contest);
		
		// спрашиваем топ
        $page->setParam('top', 
			$curl->query(
				'image/list/'.$request->get('contestRoute'),
				['order'=>'r','orderType'=>'d','limit'=>3]
			)
		);
		
		// спрашиваем страницу
        $page->setParam('list',
			$curl->query(
				'image/list/'.$request->get('contestRoute'),
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
