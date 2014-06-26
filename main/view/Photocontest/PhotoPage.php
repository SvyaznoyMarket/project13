<?php

namespace View\Photocontest;

class PhotoPage extends _Layout {
	
	public function slotContent() {
		return $this->render('photocontest/page-photo', $this->params);
	}
}
