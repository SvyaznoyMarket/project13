<?php

namespace View\Search;

class EmptyPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        if (!$this->hasParam('title')) {
            $searchQuery = $this->escape($this->getParam('searchQuery'));
            if (empty($searchQuery)) {
                $this->setParam('title', 'Пустая фраза поиска');
            }else{
                $this->setParam('title', 'Вы искали <span class="searchEmptyMark">' . $searchQuery . '</span>');
            }
        }
    }

    public function slotContent() {
        return $this->render('search/page-empty', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog search';
    }
}
