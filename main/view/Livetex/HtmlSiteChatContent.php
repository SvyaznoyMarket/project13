<?php
namespace View\Livetex;


class HtmlSiteChatContent extends HtmlBasicContent {

    protected function inCycleCondition( &$item ) {
        return isset( $item->count );
    }


    protected function inCycle( $item ) {
        $out = '';
        $out .= '<div class=""><span class="param_name">Количество чатов: </span>' . $item->count . '</div>';
        $out .= '<div class=""><span class="param_name">Количество упрощенных чатов: </span>' . $item->lost . '</div>';
        $out .= '<div class=""><span class="param_name">Среднее количество чатов за период: </span>' . $item->average . '</div>';
        $out .= '<div class=""><span class="param_name">Количество положительных оценок чатов: </span>' . $item->positive . '</div>';
        $out .= '<div class=""><span class="param_name">Количество отрицательных оценок чатов: </span>' . $item->negative . '</div>';
        return $out;
    }


}