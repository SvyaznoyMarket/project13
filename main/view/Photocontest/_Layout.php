<?php

namespace View\Photocontest;

class _Layout extends \View\DefaultLayout {

	protected $layout = 'layout-oneColumn';


	protected function prepare() {
		$this->addStylesheet('/css/photoContest/style.css');
		$this->addJavascript('/js/photocontest/code.js');
	}
	
//	public function slotMainMenu(){}

	public function slotContentHead() {
		// заголовок контента страницы
		if (!$this->hasParam('title')) {
			$this->setParam('title', 'Фотоконкурс');
		}
		// навигация
		if (!$this->hasParam('breadcrumbs')) {
			$this->setParam('breadcrumbs', []);
		}

		return $this->render('photocontest/_contentHead', $this->params);
	}
}
