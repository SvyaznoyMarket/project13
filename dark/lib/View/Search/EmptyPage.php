<?php

namespace View\Search;

class EmptyPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('search/page-empty', $this->params);
    }
}
