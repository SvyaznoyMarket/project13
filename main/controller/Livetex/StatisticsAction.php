<?php

namespace Controller\Livetex;

class StatisticsAction {
    public $date_format = 'Y-m-d';
    public $date_begin;
    public $date_end;
    private $operId;
    private $chatId;
    private $siteId = 41836;
    private $aside_menu = [];


    public function __construct() {
        /*
        $this->date_begin = (string) date($this->date_format, strtotime('-1 day'));
        $this->date_end = (string) date($this->date_format,strtotime('today UTC'));

        $this->date_begin = '2013-07-15';
        $this->date_end = '2013-07-25';*/
    }


    private function init(&$request){
        $operId = $request->get('operator');
        if ($operId) $this->operId = $operId;


        $siteId = $request->get('site');
        if ($siteId) $this->siteId = $siteId;

        $chatId = $request->get('chat');
        if ($chatId) $this->chatId = $chatId;



        $date_begin = $request->get('date_begin');
        if ($date_begin) {
            $date_begin = (string) date($this->date_format, strtotime($date_begin));
            if ($date_begin) $this->date_begin = $date_begin;
        }

        $date_end = $request->get('date_end');
        if ($date_end) {
            $date_end = (string) date($this->date_format, strtotime($date_end));
            if ($date_end) $this->date_end = $date_end;
        }


        if (empty($this->date_begin)) $this->date_begin = (string) date($this->date_format, strtotime('-1 day'));
        if (empty($this->date_end)) $this->date_end = (string) date($this->date_format,strtotime('today UTC'));

        // tmp for debug
        $this->date_begin = '2013-05-15';
        $this->date_end = '2013-07-25';


        $this->addMenu('Главная страница статистики', '/livetex-statistics');
        $this->addMenu('Статистика сайтов', '/livetex-statistics?chat=true');
        $this->addMenu('Статистика чатов', '/livetex-statistics?site=true');
        $this->addMenu('Статистика операторов', '/livetex-statistics?operators=true');

    }


    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $this->init($request);
        $page = new \View\Livetex\StatisticsPage();

        //$router = \App::router();
        //$client = \App::coreClientV2();
        //$user = \App::user();
        //$region = $user->getRegion();

        $actions = [];
        $content = [];

        include_once '../lib/LiveTex/API.php';
        $API = \LiveTex\Api::getInstance(); //LiveTex/API




        if (!empty( $this->operId )) {
            $actions[] = 'one_operator';
        }

        if ( $request->get('chat') ) {
            $actions[] = 'chat';
        }

        if ( $request->get('site') ) {
            $actions[] = 'site';
        }

        if (empty($actions)) {
            $actions[] = 'allOperators';
        }



        // for debug, temporal
        // $actions[] = 'site';



        if (in_array('chat',$actions)) {

            if ( !empty($this->operId) ) {
                $operator_chat = $API->testmethod('Operator.ChatStat', [
                    'date_begin' => $this->date_begin,
                    'date_end' => $this->date_end,
                    'operator_id' => $this->operId,
                ]);
                $this->l($operator_chat, '$operator_chat');
                $content['operator_chat'] = $operator_chat;
            }


            if ( !empty($this->siteId) ) {
                $site_chat = $API->testmethod('Site.ChatStat', [
                    'date_begin' => $this->date_begin,
                    'date_end' => $this->date_end,
                    'site_id' => $this->siteId,
                ]);

                //$this->l($site_chat,'site_chat');
                $content['site_chat'] = $site_chat;
            }

            $chat_active = $API->testmethod('Chat.GetActive', []);
            $content['chat_active'] = $chat_active;
            //$this->l($chat_active,'chat_active');

        }



        if (in_array('site',$actions)) {
            $content['site'] = $API->testmethod('Site.GetList');
        }


        if (in_array('one_operator',$actions)) {
            $one_operator = $API->testmethod('Operator.ChatStat', [
                'date_begin' => $this->date_begin,
                'date_end' => $this->date_end,
                'operator_id' => $this->operId,
                'site_id' => 41836,
            ]);
            $this->l($one_operator, 'one operator');
        }



        if (in_array('allOperators',$actions)) {
            $content['allOperators'] = $API->method('Operator.GetList');

            $operators = $API->method('Operator.GetList');
            $operators_count_html = count($operators->response);

            $page->setParam('operators', $operators);
            $page->setParam('operators_count_html', $operators_count_html);
        }


        $page->setParam('content', $content);
        $page->setParam('actions', $actions);
        $page->setParam('aside_menu', $this->aside_menu);
        $page->setParam('date_begin', $this->date_begin);
        $page->setParam('date_end', $this->date_end);

        return new \Http\Response( $page->show() );
    }




    // for sidebar menu BEGIN

    public function addMenu($name, $link){
        if (!empty($name))
            if (!empty($link))
                $this->aside_menu[] = ['name' => $name, 'link' => $link ];
    }

    public function setMenu($name, $link){
        if (!empty($name))
            if (!empty($link))
                $this->aside_menu[$name] = $link;
    }

    public function getMenu($name){
        if (!empty($name))
            if ( isset( $this->aside_menu[$name] ) )
                return $this->aside_menu[$name];
    }

    // for sidebar menu END




    // temporal log, debug function
    private function l(&$var, $name = null){
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



    public function slotMainMenu() { return ''; }
    public function slotUserbar() { return ''; }
    public function slotMyThings() { return ''; }

}