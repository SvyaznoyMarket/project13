<?php

namespace View\Livetex;


class HtmlBasicContent{
    protected $default_ava = '9c46526d320a87cdab6dfdbc14f23cdc.png';
    protected $page_url = '/livetex-statistics';
    protected $more_word = '[ Подробнее » ]';
    protected $head_text = '';
    protected $big_head = 'LiveTex: Статистика.';
    protected $helper = '';


    public function __construct( $big_head = null, $head_text = null) {
        if ( $big_head ) $this->big_head = $big_head;
        if ( $head_text ) $this->head_text = $head_text;
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
                return $out;
            }
            return $this->noresponse();
        }
        return $this->nodata();

    }
    //////////////////////////////////////////////////////


    protected function inCycleCondition( &$item ) {
        return isset( $item );
    }


    protected function inCycle( $item ) {
        $out = '<div class="basic_html"> [ <pre>' . print_r($item,1) . '</pre> ] </div>';
        return $out;
    }


    protected function head() {
        return '<h3>' . $this->head_text . '</h3>';
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



}