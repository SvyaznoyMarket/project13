<?php

namespace View\Tag;

class BranchPage extends Layout {
    public function slotContent() {
        return $this->render('tag/page-branch', $this->params);
    }
}
