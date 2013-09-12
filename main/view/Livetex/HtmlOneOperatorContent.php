<?php
namespace View\Livetex;


class HtmlOneOperatorContent extends HtmlBasicContent {

    protected function inCycleCondition( &$item ) {
        return isset( $item->count );
    }


    protected function inCycle( $item ) {
        if ( isset($this->params['stat_params']['operId']['value']) ) {
            $operId = $this->params['stat_params']['operId']['value'];
        }

        $out = '';
        if ($operId) $out .= '<h3>' . $this->operator_info($operId, 'name') . " (ID: $operId)" . '</h3>';
        $out .= '<div class="one_oper"><span class="param_name">Количество чатов: </span>' . $item->count . '</div>';
        $out .= '<div class="one_oper"><span class="param_name">Количество упущенных чатов: </span>' . $item->lost . '</div>';
        $out .= '<div class="one_oper"><span class="param_name">Среднее количество чатов за период: </span>' . $item->average . '</div>';
        $out .= '<div class="one_oper"><span class="param_name">Количество положительных оценок чатов: </span>' . $item->positive . '</div>';
        $out .= '<div class="one_oper"><span class="param_name">Количество отрицательных оценок чатов: </span>' . $item->negative . '</div>';
        return $out;
    }


}