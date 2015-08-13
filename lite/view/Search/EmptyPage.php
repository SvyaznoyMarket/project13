<?php

namespace view\Search;


use View\Search\IndexPage;

class EmptyPage extends IndexPage
{

    public function blockContent() {
        return $this->render('category/_search', $this->params);
    }

}