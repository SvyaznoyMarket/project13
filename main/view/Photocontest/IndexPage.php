<?php

namespace View\Photocontest;

class IndexPage extends _Layout {

	public function slotContent() {
		return $this->render('photocontest/page-index', $this->params);
	}
}
