<?php

namespace View\Tag;

class LeafPage extends Layout {
    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('tag/page-leaf', $this->params);
    }
}
