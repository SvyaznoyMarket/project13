<?php

namespace View\Photocontest;

class PhotoPage extends Layout {
	
	public function slotContent() {
		return $this->render('photocontest/page-photo', $this->params);
	}
}
