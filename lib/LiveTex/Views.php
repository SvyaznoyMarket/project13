<?php

namespace LiveTex;


class Views {
    public $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';

    public function getOperators($operators) {
        $out = false;

        if ($operators and $operators->response) {
            $out = '';
            $operators = $operators->response;

            foreach ($operators as $op) {
                $isonline = $op->isonline ? 'Да' : 'Нет';

                $ava = $op->photo;
                if (!$ava) $ava = $this->default_ava;

                $out .= '<li class="li_oper">';

                $out .= '<div class="ava_oper"><img src="//cs15.livetex.ru/'.$ava.'" class="img_ava"></div>';
                    $out .= '<div class="name_oper"><span class="param_name">Имя: </span>'.$op->firstname.' '.$op->lastname.'</div>';
                    $out .= '<div class="id_oper"><span class="param_name">ID: </span>'.$op->id.'</div>';
                    //$out .= '<div class="depart_oper"><span class="param_name">Departments: </span>44230</div>';
                    $out .= '<div class="state_oper"><span class="param_name">State_id: </span>'.$op->state.'</div>';
                    $out .= '<div class="state_oper"><span class="param_name">Онлайн: </span>'.$isonline.'</div>';
                    //$out .= '<div class="iscall_oper"><span class="param_name">Call: </span>есть</div>';
                $out .= '</li>';

            }
        }

        return $out;
    }
}