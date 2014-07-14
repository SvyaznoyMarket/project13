<?php

namespace View\Game;

class Layout extends \View\DefaultLayout {

	protected $layout = 'layout-oneColumn';

//	public function slotMainMenu(){}

	public function slotContentHead() {
		return $this->render('photocontest/_contentHead', $this->params);
	}
}
