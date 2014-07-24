<?php

namespace View\Photocontest;

class Layout extends \View\DefaultLayout {

	protected $layout = 'layout-oneColumn';


	protected function prepare() {
		$this->addStylesheet('/css/photoContest/style.css');
		$this->addJavascript('/js/photocontest/code.js');
        $this->initMeta();
	}
    
    
    protected function initMeta() {
        // Устанавливаем тэги для парсинга социальными сервисами при репосте
        $this->addMeta('og:url', \App::request()->getUri());
        
        // @todo в следующей версии согласовать с заказчиками что им нужно, как формировать
        $this->setTitle('Выиграй битву трансформеров!');
        $this->addMeta('title', 'Выиграй битву трансформеров!');
        $this->addMeta('og:title', 'Выиграй битву трансформеров!');
        
        $this->addMeta('description', 'Сфотографируй битву с участием своих трансформеров. Загрузи фотографию на страницу конкурса. Победи!');
        $this->addMeta('og:description', 'Сфотографируй битву с участием своих трансформеров. Загрузи фотографию на страницу конкурса. Победи!');
        
        // @todo в следующей версии добавить
        $this->addMeta('image_src', 'http://enter.ru/css/photoContest/i/transOtake.jpg');
        $this->addMeta('og:image', 'http://enter.ru/css/photoContest/i/transOtake.jpg');
    }
    
    
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
