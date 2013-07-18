<?php

namespace View\Tag;

class RootPage extends Layout {
    public function slotContent() {
        return $this->render('tag/page-root', $this->params);
    }
}
