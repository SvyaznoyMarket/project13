<?php

namespace Controller\Photocontest;

class ServiceAction {
	
	/**
	 * Получение ключа безопасности для выполнения некоторых действий
	 * 
	 * @param \Http\Request $request
	 * @return type
	 */
	public function safeKey(\Http\Request $request){
		//\App::logger()->debug('Exec ' . __METHOD__);
		
		$curl = \App::photoContestClient();
		$r = $curl->query('contest/lastActive');
		
		var_dump($r);
	}
}
