<?php

namespace View\Livetex;

class StatisticsPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';
    public $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';
    private $noitems_msg = '<em class="error">Данные не получены. Ответ сервера LiveTex: </em>';


    public function __construct() {
        parent::__construct();
        $this->addMeta('viewport', 'width=900');
        $this->setTitle('Enter — LiveTex Статистика');
        $this->addStylesheet('/css/global.min.css');
        $this->addJavascript(\App::config()->debug ? '/js/loadjs.js' : '/js/loadjs.min.js');
    }



    public function slotContent() {
        $html_out = '';

        $content = $this->params['content'];

        foreach ($content as $key => $value) {
            $funcname = 'slot_'.$key."_Html";
            if ( method_exists($this, $funcname) ) {
                $html_out .= $this->$funcname($value);
            }
        }

        $html_out = $this->slotSidebar() . '<div class="bPromoCatalog lts_wrap">'.$html_out.'</div><div class="clear"></div>';

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
                $out .= '<div class="lts_name name_oper"><span class="param_name">Имя: </span>' . $op->firstname . ' ' . $op->lastname . '</div>';
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



    /*
     * Форміруем слот с html с инфой об выбранном операторе
     */
    public function slot_oneOperator_Html($response = null)
    {
        $out = false;

        if ($response and $response->response) {
            $out = '';
            $response = $response->response;

            if ( isset($response->message) ) {
                return '<div class="noitems">' . $this->noitems_msg . $response->message . '</div>';
            }

            foreach ($response as $item) {
                if (isset($item->count)) {
                    $out .= '<div class="lts_item">';
                    $out .= '<div class="id_oper"><span class="param_name">Количество чатов: </span>' . $item->count . '</div>';
                    $out .= '<div class="id_oper"><span class="param_name">Количество упущенных чатов: </span>' . $item->lost . '</div>';
                    $out .= '<div class="id_oper"><span class="param_name">Среднее количество чатов за период: </span>' . $item->average . '</div>';
                    $out .= '<div class="id_oper"><span class="param_name">Количество положительных оценок чатов: </span>' . $item->positive . '</div>';
                    $out .= '<div class="id_oper"><span class="param_name">Количество отрицательных оценок чатов: </span>' . $item->negative . '</div>';
                    $out .= '</div>';
                }else{
                    return '<div class="noitems">' . $this->noitems_msg . print_r($item,1) . '</div>';
                }
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


}
