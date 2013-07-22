<?php

namespace Controller\Livetex;

class StatisticsAction {
    public $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';
    public $date_format = 'Y-m-d';
    public $date_begin;
    public $date_end;
    private $operId;


    public function __construct() {
        $this->date_begin = (string) date($this->date_format, strtotime('-1 day'));
        $this->date_end = (string) date($this->date_format,strtotime('today UTC'));

        $this->date_begin = '2013-07-15';
        $this->date_end = '2013-07-25';
    }


    private function init(&$request){
        $operId = $request->get('operatorId');
        if ($operId) $this->operId = $operId;




        $date_begin = $request->get('date_begin');
        if ($date_begin) {
            $date_begin = (string) date($this->date_format, strtotime($date_begin));
            if ($date_end) $this->date_begin = $date_begin;
        }

        $date_end = $request->get('date_end');
        if ($date_end) {
            $date_end = (string) date($this->date_format, strtotime($date_end));
            if ($date_end) $this->date_end = $date_end;
        }

    }


    public function execute(\Http\Request $request) {
        $this->init($request);
        \App::logger()->debug('Exec ' . __METHOD__);

        //$router = \App::router();
        //$client = \App::coreClientV2();
        //$user = \App::user();
        //$region = $user->getRegion();
        $page = new \View\Livetex\StatisticsPage();

        $action = 'all_operators';

        include_once '../lib/LiveTex/API.php';
        $API = \LiveTex\Api::getInstance(); //LiveTex/API


        $operators = $API->method('Operator.GetList');
        $operators_html = $this->operatorsHtml($operators);
        $operators_count_html = count($operators->response);
        $page->setParam('operators_html', $operators_html);
        $page->setParam('operators_count_html', $operators_count_html);





        if (!empty( $this->operId )) {
            $one_operator = $API->method('Operator.ChatStat', [
                'date_begin' => $this->date_begin,
                'date_end' => $this->date_end,
                'operator_id' => $operId
            ]);
            print '###';
            print_r($one_operator);
            $action = 'one_operator';
        }


        $page->setParam('action', $action);

        return new \Http\Response($page->show());
    }




    public function operatorsHtml($operators) {
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