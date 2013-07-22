<?php

namespace View\Livetex;

class StatisticsPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';


    public function __construct() {
        parent::__construct();
        $this->addMeta('viewport', 'width=900');
        $this->setTitle('Enter — LiveTex Статистика');
        $this->addStylesheet('/css/global.min.css');
        $this->addJavascript(\App::config()->debug ? '/js/loadjs.js' : '/js/loadjs.min.js');
    }



    public function slotContent() {
        switch ( $this->params['action'] ){
            case 'one_operator':
                return $this->render('partner-counter/livetex/stat_one_operator', $this->params);

            default:
                return $this->render('partner-counter/livetex/stat_all_operators', $this->params);
        }
    }


    public function slotInnerJavascript() {

        $return = $this->render('partner-counter/livetex/_stat-head', ['tag_params' => []]);
        // todo: move this in js and css files

        return $return;
    }


}
