<?php

namespace View\Livetex;

class StatisticsPage extends \View\DefaultLayout {
    protected $layout  = 'partner-counter/livetex/layout-mini';
    public $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';
    private $noitems_msg = '<em class="error">Данные не получены. Ответ сервера LiveTex: </em>';
    private $noisset = '<em class="error">Данные не получены.</em>';
    private $page_url = '/livetex-statistics';
    private $more_w1ord = ' [ Подробнее » ] ';
    public $helper;


    public function __construct() {
        parent::__construct();
        $this->addMeta('viewport', 'width=900');
        $this->setTitle('Enter — LiveTex Статистика');
        //$this->addStylesheet('/css/global.min.css');
        //$this->addJavascript(\App::config()->debug ? '/js/loadjs.js' : '/js/loadjs.min.js');
        $this->helper = new \Helper\TemplateHelper();
    }


    // HTML wrapper
    public function wr(&$content, $class = null, $tag = 'div') {
        return $this->helper->wrap($content, $class, $tag);
    }


    public function slotContent() {
        $html_out = '';

        $content = $this->params['content'];

        // Делегируем всё построение контента функциям, названия которых и параметры запуска которых храняться в $content
        foreach ($content as $key => $value) {
            $funcname = 'slot_'.$key."_Html";
            if ( method_exists($this, $funcname) ) {
                $html_out .= $this->$funcname($value);
            }
        }

        $html_out = $this->slotSidebar() . $this->wr($html_out,'bPromoCatalog');
        $html_out = $this->wr($html_out, 'lts_wrap');
        $html_out .= '<center> <p></p> <p>< -- end of Statistics page --></p> <p><img /></p> </center>';

        return $html_out;
    }



    public function slotSidebar( $params_arr = [] ) {
        return $this->render('partner-counter/livetex/stat_sidebar', $this->params + $params_arr);
    }


    public function slotInnerJavascript() {
        $return = $this->render('partner-counter/livetex/_stat-head', ['tag_params' => []]);
        // todo: move this in js and css files
        return $return;
    }




    public function slot_allOperators_Html($operators = null)
    {
        $out = false;

        if ($operators and $operators->response) {
            $out = '';
            $operators = $operators->response;

            $out .= '<ul id="operators">';
            foreach ($operators as $op) {
                if (!isset($op->isonline)) { // if error
                    return '<div class="noitems">' . $this->noitems_msg . $op . '</div>';
                }

                $isonline = $op->isonline ? '<span class="isonline">Да</span>' : 'Нет';

                $ava = $op->photo;
                if (!$ava) $ava = $this->default_ava;

                $out .= '<li class="lts_li li_oper">';

                $out .= '<div class="ava_oper"><img src="//cs15.livetex.ru/' . $ava . '" class="img_ava"></div>';

                $op_name = $op->firstname . ' ' . $op->lastname;
                $a_link = '<a href="'.$this->makeUrl(['operId' => $op->id, 'actions' => 'oneOperator' ]).'">' . $op_name . '</a>';
                $out .= '<div class="lts_name name_oper"><span class="param_name">Имя: </span>'.$a_link.'</div>';

                $out .= '<div class="id_oper"><span class="param_name">ID: </span>' . $op->id . '</div>';
                //$out .= '<div class="depart_oper"><span class="param_name">Departments: </span>44230</div>';
                $out .= '<div class="state_oper"><span class="param_name">State_id: </span>' . $op->state . '</div>';
                $out .= '<div class="state_oper"><span class="param_name">Онлайн: </span>' . $isonline . '</div>';
                //$out .= '<div class="iscall_oper"><span class="param_name">Call: </span>есть</div>';
                $out .= '</li>';

            }
            $out .= '</ul>';
        }

        $html_out = $this->render('partner-counter/livetex/stat_allOperators', $this->params + ['htmlcontent' => $out]);

        return $html_out;
    }



    private function error($mes = '', &$var = null) {
        $res = '<em class="error">Данные не получены.' . (string) $mes . '</em>';
        if ( isset( $var ) and !empty($var) ) $res .= ' ##{ '.print_r($var,1) . ' }### ';
        return $res;
    }



    private function operator_info($that) {

        if ( !isset( $this->params['stat_params']['operId']['value'] ) ) return $this->noisset;

        $operId = $this->params['stat_params']['operId']['value'];

        if ( !isset( $this->params['operators_list'][$operId] ) ) return  $this->error('В массиве операторов не найден '.$operId.' оператора.');


        if ($that == 'name') {
            $firstname = $this->params['operators_list'][$operId]->firstname;
            $lastname = $this->params['operators_list'][$operId]->lastname;
            return $firstname.' '.$lastname;
        }else{
            if ( isset( $this->params['operators_list'][$operId]->$that ) ) {
                return $this->params['operators_list'][$operId]->$that;
            }else{
                $this->error('Не найдено свойство оператора: '.$that);
            }
        }

        return false;
    }


    /*
     * Формируем слот с html с инфой об выбранном операторе
     */
    public function slot_oneOperator_Html($response = null)
    {
        $out = false;

        if ($response){
            $out = '';
            $out .= '<h3>' .$this->operator_info('name') . ' (ID:' . $this->operator_info('id') . ')' . '</h3>';


            if ($response->response) {

                $response = $response->response;

                if ( isset($response->message) ) {
                    return '<div class="noitems">' . $this->noitems_msg . $response->message . '</div>';
                }

                foreach ($response as $item) {
                    if (isset($item->count)) {
                        $out .= '<div class="lts_item">';
                        $out .= '<div class="one_oper"><span class="param_name">Количество чатов: </span>' . $item->count . '</div>';
                        $out .= '<div class="one_oper"><span class="param_name">Количество упущенных чатов: </span>' . $item->lost . '</div>';
                        $out .= '<div class="one_oper"><span class="param_name">Среднее количество чатов за период: </span>' . $item->average . '</div>';
                        $out .= '<div class="one_oper"><span class="param_name">Количество положительных оценок чатов: </span>' . $item->positive . '</div>';
                        $out .= '<div class="one_oper"><span class="param_name">Количество отрицательных оценок чатов: </span>' . $item->negative . '</div>';
                        $out .= '</div>';
                    }else{
                        return '<div class="noitems">' . $this->noitems_msg . print_r($item,1) . '</div>';
                    }
                }

            }else{
                $out .= '<div class="lts_item">';
                $out .= '<div class="one_oper">Нет данных. Вероятно за указаное время статистика отсутствует. Попробуйте увеличить интервал между датами.</div>';
                $out .= '</div>';
            }
        }

        $html_out = $this->render('partner-counter/livetex/stat_oneOperator', $this->params + ['htmlcontent' => $out]);

        return $html_out;
    }



    public function slot_site_Html($site = null)
    {

        $out = false;
        if ($site and isset($site->response)) {
            $out = '';

            $out .= '<ul>';
            foreach ($site->response as $item) {
                if (!isset($item->id)) { // if error
                    return '<div class="noitems">' . $this->noitems_msg . $item . '</div>';
                }


                $out .= '<li class="lts_li li_site">';

                $out .= '<div class="lts_name url_site"><span class="param_name">Имя: </span>' . $item->url . '</div>';
                $out .= '<div class="id_oper"><span class="param_name">ID: </span>' . $item->id . '</div>';

                $isembed = $item->isembed ? 'встроенный чат' : 'большой чат';
                $melody = $item->melody ? 'is melody' : 'No is melody';

                $out .= '<div class="state_oper"><span class="param_name">Тип: </span>' . $isembed . '</div>';
                $out .= '<div class="state_oper"><span class="param_name">Melody: </span>' . $melody . '</div>';

                $out .= '</li>';

            }
            $out .= '</ul>';
        };


        $html_out = $this->render('partner-counter/livetex/stat_site', $this->params + ['htmlcontent' => $out]);

        return $html_out;
    }



    public function slot_site_chat_Html($site = null)
    {
        $out = false;
        if ($site and isset($site->response)) {
            $out = '';
            $out .= '<ul>';
            foreach ($site->response as $item) {
                $out .= '<li class="lts_li li_site">';

                if (is_string($item)) { // if error
                    return '<div class="noitems">' . $this->noitems_msg . $item . '</div>';
                }

                $out .= '<div class=""><span class="param_name">Количество чатов: </span>' . $item->count . '</div>';
                $out .= '<div class=""><span class="param_name">Количество упрощенных чатов: </span>' . $item->lost . '</div>';
                $out .= '<div class=""><span class="param_name">Среднее количество чатов за период: </span>' . $item->average . '</div>';
                $out .= '<div class=""><span class="param_name">Количество положительных оценок чатов: </span>' . $item->positive . '</div>';
                $out .= '<div class=""><span class="param_name">Количество отрицательных оценок чатов: </span>' . $item->negative . '</div>';

                $out .= '</li>';
            }
            $out .= '</ul>';
        };
        $html_out = $this->render('partner-counter/livetex/stat_site_chat', $this->params + ['htmlcontent' => $out]);
        //return $out;
        return $html_out;

    }


    private function makeUrl($params = [])
    {
        foreach ($params as $key => $value) {
            if (!empty($key)) {
                $this->params['stat_params'][$key]['value'] = $value;
            }
        }

        $i = 0;
        $link = $this->page_url;
        if (isset($this->params['stat_params']))
            foreach ($this->params['stat_params'] as $key => $value) {
                $val = $value['value'];
                if (!empty($key) && !empty($val)) {
                    $i++;
                    $seporator = '&';
                    if ($i == 1) $seporator = '?';
                    if (is_array($val)) $val = implode('|',$val);
                    $link .= $seporator . $key . '=' . $val;

                    //print '<pre>';
                    //print_r($link);
                    //print '</pre>';
                }
            }

        return $link;
    }


}