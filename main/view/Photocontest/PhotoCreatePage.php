<?php

namespace View\Photocontest;

class PhotoCreatePage extends _Layout {
	
	public function slotContent() {
		return $this->render('photocontest/page-photo-create', $this->params);
	}
}
