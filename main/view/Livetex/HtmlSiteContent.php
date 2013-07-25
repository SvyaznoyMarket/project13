<?php
namespace View\Livetex;


class HtmlSiteContent extends HtmlBasicContent {

    protected function inCycleCondition( &$item ) {
        return isset( $item->id );
    }


    protected function inCycle( $item ) {
        $out = '';
        $out .= '<div class="lts_name url_site"><span class="param_name">Имя: </span>' . $item->url . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">ID: </span>' . $item->id . '</div>';

        $isembed = $item->isembed ? 'встроенный чат' : 'большой чат';
        $melody = $item->melody ? 'is melody' : 'No is melody';

        $out .= '<div class="state_oper"><span class="param_name">Тип: </span>' . $isembed . '</div>';
        $out .= '<div class="state_oper"><span class="param_name">Melody: </span>' . $melody . '</div>';

        return $out;
    }


}