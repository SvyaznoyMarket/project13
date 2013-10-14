<?php

namespace Terminal\View\Search;

class IndexPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('search/page-index', $this->params);
    }
}
