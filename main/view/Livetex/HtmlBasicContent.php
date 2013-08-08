<?php

namespace View\Livetex;


class HtmlBasicContent{
    protected $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';
    protected $date_format = 'Y-m-d';
    protected $page_url = '/livetex-statistics';
    protected $more_word = '[ Подробнее » ]';
    protected $small_head = '';
    protected $big_head = 'LiveTex: Статистика.';
    protected $helper = '';
    protected $params;


    public function __construct( $big_head = null, $small_head = null, $params = null) {
        if ( $big_head ) $this->big_head = $big_head;
        if ( $small_head ) $this->small_head = $small_head;
        if ( $params ) $this->params = $params;
        $this->helper = new \Helper\TemplateHelper();
    }

    // HTML wrapper
    protected function wr(&$content, $class = null, $tag = 'div') {
        return $this->helper->wrap($content, $class, $tag);
    }

    //////////////////////////////////////////////////////
    public function content($big_response) {
        $out = (string) $this->big_head;
        if ($out) $out = $this->wr($this->big_head, 'lts_head bPromoCatalog_eName', 'h2');

        if ($big_response){
            $out .= (string) $this->head();

            if ($big_response->response) {
                $res = $big_response->response;

                if ( isset($big_response->error) and !empty($big_response->error) and isset($res->message) ) {
                    $err = $this->noitems( $res->message,  $big_response->error );
                    $out .= $this->wr ( $err, 'lts_item' );
                    return $out;
                }

                $tobe_ul = false; if ( count( $res ) > 1 ) $make_ul = true;

                if ( $tobe_ul ) $out .= '<ul>';

                ///////////////////////////
                foreach ($res as $item) {
                    if ( $this->inCycleCondition( $item) ) {
                        if ( $tobe_ul ) $out .= '<li class="lts_li">';
                        $out .= '<div class="lts_item">';
                        $out .= $this->inCycle($item);
                        $out .= '</div>';
                        if ( $tobe_ul ) $out .= '</li>';
                    }else{
                        return $this->noitems($item);
                    }
                } // end of foreach
                ///////////////////////////



                if ( $tobe_ul ) $out .= '</ul>';


                $analytics  = $this->analytics();
                if ( !empty($analytics) ) {
                    $hd = 'LiveTex: Рассчитанная статистика';
                    $analytics = $this->wr( $analytics, 'lts_item lts_analytics' );
                    $analytics = $this->wr( $hd, 'lts_head lts_analytics_head bPromoCatalog_eName', 'h2' ) . $analytics;
                    $out = $analytics . $out;
                }

                return $out;
            }
            return $this->noresponse();
        }
        return $this->nodata();

    }
    //////////////////////////////////////////////////////


    protected function analytics() { // прототип функции
        return false;
    }

    protected function inCycleCondition( &$item ) {
        return isset( $item );
    }


    protected function inCycle( $item ) {
        $out = '<div class="basic_html"> [ <pre>' . print_r($item,1) . '</pre> ] </div>';
        return $out;
    }


    protected function head() {
        $out = false;
        if ( !empty($this->small_head) ) {
            $out = '<h3>' . $this->small_head . '</h3>';
        }
        return $out;
    }



    protected function nodata() {
        $out = '';
        $out .= '<div class="lts_item">';
        $out .= '<div class="noresponse">Нет ответа. Попробуйте обновить страницу немного позже. Возможно, вы слишком часто обращаетесь к API LiveTex</div>';
        $out .= '</div>';
        return $out;
    }


    protected function noresponse() {
        $out = '';
        $out .= '<div class="lts_item">';
        $out .= '<div class="one_oper">Нет данных. Вероятно за указаное время статистика отсутствует. Попробуйте увеличить интервал между датами.</div>';
        $out .= '</div>';
        return $out;
    }


    protected function noitems($item, $error = null ) {
        $noitems_msg = '<em class="error">Данные не получены. Ответ сервера LiveTex: </em>';
        $error_msg = $error ? '<div class="error_code">Код ошибки: ' . $error . '</div>' : '' ;
        $out = '<div class="noitems">' . $noitems_msg . "\n [ " . print_r($item,1) ." ] \n". $error_msg . '</div>';
        return $out;
    }



    protected function error($error = null ) {
        $out = '<em class="error">Данные не получены. Ответ сервера LiveTex: </em>';
        if ( $error ) $out .= '<em class="error">'.$error.'</em>';
        return $out;
    }



    protected function makeUrl($params = [])
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
                }
            }

        return $link;
    }



    protected function operator_info( $operId, $attr = 'name' ) {
        if (!$operId) return $operId;
        $operators_list = $this->params['operators_list'];

        if ( !isset( $operators_list[$operId] ) ) return  $this->error('В массиве операторов не найден '.$operId.' оператора.');

        if ($attr == 'name') {
            $firstname = $this->params['operators_list'][$operId]->firstname;
            $lastname = $this->params['operators_list'][$operId]->lastname;
            return $firstname.' '.$lastname;
        }else{
            if ( isset( $this->params['operators_list'][$operId]->$attr ) ) {
                return $this->params['operators_list'][$operId]->$attr;
            }else{
                //$this->error('Не найдено свойство оператора: '.$attr);
                return $operId;
            }
        }

        return false;
    }



    protected function operator_link( $operId, $inlink ) {
        if ( $operId and $inlink) {
            $a_link = '<a href="'.$this->makeUrl(['operId' => $operId, 'actions' => 'OneOperator' ]).'">' . $inlink . '</a>';
            return $a_link;
        }
        return false;
    }




    protected function timeInSeconds($time) {
        if ( !is_numeric($time) ) {
            $time = (string) $time;
            $answ = explode( ':', $time );

            if ( is_array($answ) and isset($answ[1]) and isset($answ[2]) ) {
                $h = (int) $answ[0];
                $m = (int) $answ[1];
                $s = (int) $answ[2];

                if ($h) $m = $m + $h / 60;
                if ($m) $s = $s + $m / 60;

                return $s;
            }
            return $time;

        }else{
            return $time;
        }
    }


    protected function timeFromSeconds($time) {
        $m = $h = null;
        $s = (int) $time;
        if ( $s > 60 ) {
            $m = (int) ( $s / 60 );
            $s = $s % 60;
            if ( $m > 60 )  {
                $h = (int) ( $m / 60 );
                $m = $m % 60;
            }
        }

        $ret = "$s сек";
        if ( $m ) {
            $ret = "$m мин " . $ret;
            if ( $h ) {
                $ret = "$h ч " . $ret;
            }
        }
        $ret = '<time>' . $ret. '</time>';
        return $ret;
    }


    /*
    * Временный метод, используется для вывода отладочной инфы
    */
    protected function l(&$var, $name = null){
        if ($name) {
            try{
                print PHP_EOL."\n### [".$name."] ###\n".PHP_EOL;
            }catch(Exception $er){
                print PHP_EOL."\n### [ *error in _name_* ] ###\n".PHP_EOL;
            }

        }
        print '<pre>';
        try{
            $ret = print_r($var);
        }catch(Exception $er){
            print PHP_EOL."\n *Exception: variable not exist* \n".PHP_EOL;
        }
        print '</pre>';
        print "\n".PHP_EOL;

        return $ret;
    }

}