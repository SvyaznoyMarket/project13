<?php

namespace view\OrderV3;


class NewPage extends \View\OrderV3\Layout
{

    public function blockContent() {
        return $this->render('order/page-new');
    }

}