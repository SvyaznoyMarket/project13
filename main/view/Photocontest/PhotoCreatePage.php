<?php

namespace View\Photocontest;

class PhotoCreatePage extends Layout {
	
	public function slotContent() {
		return $this->render('photocontest/page-photo-create', $this->params);
	}
}
