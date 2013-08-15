<?php

namespace View\Livetex;

class StatisticsPage extends \View\DefaultLayout {
    protected $layout  = 'partner-counter/livetex/layout-mini';
    public $helper;

    public function __construct() {
        parent::__construct();
        $this->addMeta('viewport', 'width=900');
        $this->setTitle('Enter — LiveTex Статистика');
        //$this->addStylesheet('/css/global.min.css');
        //$this->addJavascript(\App::config()->debug ? '/js/loadjs.js' : '/js/loadjs.min.js');
        $this->helper = new \Helper\TemplateHelper();
    }


    public function slotContent() {
        $html_out = '';
        $content = $this->params['content'];
        $heads = $this->params['heads'];
        $infomess = $this->params['infomess'];


        foreach ($content as $key => $value) {
            /**
             * Используем цикл, т.к. может быть несколько сущностей понадобиться выводить
             */

            // Делегируем всё построение контента функциям, названия которых и параметры запуска которых храняться в $content
            /*$funcname = 'slot_'.$key."_Html";
            if ( method_exists($this, $funcname) ) {
                $html_out .= $this->$funcname($value);
            }*/

            // Делегируем всё построение контента классам (типа фабрика),
            $htmlClass = '\View\Livetex\Html' . $key . 'Content';
            if (!class_exists($htmlClass)) {
                $htmlClass = '\View\Livetex\HtmlBasicContent'; // от HtmlBasicContent наследуются др. классы
            }

            $big_head = isset($heads[$key]['big_head']) ? $heads[$key]['big_head'] : '';
            $small_head = isset($heads[$key]['small_head']) ? $heads[$key]['small_head'] : '';
            $htmlView = new $htmlClass($big_head, $small_head, $this->params);
            $out = $htmlView->content($value); // метод content() есть в любом классе

            $html_out .= $this->render('partner-counter/livetex/stat_content', $this->params + ['htmlcontent' => $out]);

        }

        $html_infomess = '';
        if ( !empty($infomess) ) {
            $html_infomess = $this->wr( $infomess,'info_block infomess' ); // информационное сообщение, если задано
        }
        $html_out = $html_infomess.$html_out;
        $html_out = $this->slotSidebar() . $this->wr($html_out,'bPromoCatalog'); // добавим сайдбар
        $html_out = $this->wr($html_out, 'lts_wrap'); // обернём во враппер
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



    private function error($mes = '', &$var = null) {
        $res = '<em class="error">Данные не получены.' . (string) $mes . '</em>';
        if ( isset( $var ) and !empty($var) ) $res .= ' ##{ '.print_r($var,1) . ' }### ';
        return $res;
    }



    /*
    private function operator_info($that) {
        if ( !isset( $this->params['stat_params']['operId']['value'] ) ) return $this->error;
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
    */



    /**
     * // HTML wrapper
     * @param $content
     * @param string $tag
     * @param null $class
     * @return string
     */
    public function wr( &$content, $attr, $tag = 'div') {
        if ( !empty($tag) ) {
            $res = '<'.$tag;

            if ($attr) {
                if (is_string($attr)){
                    if ( !empty($attr) ) $res .= ' class="'.$attr.'"';
                }else
                    if ( is_array($attr) ) {
                        foreach($attr as $key => $val):
                            if ( !empty($key) and !empty($val) ):
                                $res .= ' '.$key = $val;
                            endif;
                        endforeach;
                    }
            }

            $res .= '>';
            $res .= (string) $content;
            $res .= '</'.$tag.'>';
            return $res;
        }
        return $content;
    }




    /*
     * Обнулим/переопределим некоторые ненужные на странице статистики унаследованные функции из родительского класса,
     * чтобы возможно немного быстрее страница грузилась
     */
    public function slotMainMenu() { return ''; }
    public function slotUserbar() { return ''; }
    public function slotMyThings() { return ''; }

}