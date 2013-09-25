<?php
namespace View\Livetex;


class HtmlChatActiveContent extends HtmlBasicContent {


    protected function inCycle( $item ) {
        $out = '';
        $out .= '<div class=""><span class="param_name">Чат: </span>' . $item . '</div>';
        return $out;
    }

}