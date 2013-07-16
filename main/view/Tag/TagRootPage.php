<?php

namespace View\Tag;

class TagRootPage extends Layout {
    public function slotContent() {
        return $this->render('tag/tag-root', $this->params);
    }
}
