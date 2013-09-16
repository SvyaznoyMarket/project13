<?php
namespace View\Livetex;


class HtmlAllOperatorsContent extends HtmlBasicContent {

    protected function inCycleCondition( &$item ) {
        return isset( $item->isonline );
    }


    protected function inCycle( $op ) {
        $out = '';
        $isonline = $op->isonline ? '<span class="isonline">Да</span>' : 'Нет';

        $ava = $op->photo;
        if (!$ava) $ava = $this->default_ava;

        $out .= '<li class="lts_li li_oper">';
        $out .= '<div class="ava_oper"><img src="//cs15.livetex.ru/' . $ava . '" class="img_ava"></div>';

        $op_name = $op->firstname . ' ' . $op->lastname;
        //$a_link = '<a href="'.$this->makeUrl(['operId' => $op->id, 'actions' => 'oneOperator' ]).'">' . $op_name . '</a>';
        $a_link = $op_name . '  <a href="'.$this->makeUrl(['operId' => $op->id, 'actions' => 'oneOperator' ]).'">' . $this->more_word . '</a>';
        $out .= '<div class="lts_name name_oper"><span class="param_name">Имя: </span>'.$a_link.'</div>';

        $out .= '<div class="id_oper"><span class="param_name">ID: </span>' . $op->id . '</div>';
        //$out .= '<div class="depart_oper"><span class="param_name">Departments: </span>44230</div>';
        $out .= '<div class="state_oper"><span class="param_name">State_id: </span>' . $op->state . '</div>';
        $out .= '<div class="state_oper"><span class="param_name">Онлайн: </span>' . $isonline . '</div>';
        //$out .= '<div class="iscall_oper"><span class="param_name">Call: </span>есть</div>';
        $out .= '</li>';

        return $out;
    }


}