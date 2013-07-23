<?php

namespace Controller\Livetex;

class StatisticsAction {
    private $date_format = 'Y-m-d';
    private $date_begin = '2013-07-15';
    private $date_end = '2013-07-23';
    private $operId = null;
    private $chatId = null;
    private $siteId = 41836; // TODO: move in config
    private $aside_menu = [];


    public function __construct() {
        /*
        $this->date_begin = (string) date($this->date_format, strtotime('-1 day'));
        $this->date_end = (string) date($this->date_format,strtotime('today UTC'));

        $this->date_begin = '2013-07-15';
        $this->date_end = '2013-07-25';*/
    }


    private function init(&$request){
        $operId = $request->get('operId');
        if ($operId) $this->operId = $operId;


        $siteId = $request->get('siteId');
        if ($siteId) $this->siteId = $siteId;

        $chatId = $request->get('chatId');
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
        //$this->date_begin = '2013-05-15';
        //$this->date_end = '2013-07-25';


        $this->addMenu('Общая статистика', '/livetex-statistics');
        $this->addMenu('Статистика чатов', '/livetex-statistics?chat=true');
        $this->addMenu('Статистика операторов', '/livetex-statistics?operators=true');
        $this->addMenu('Статистика сайтов', '/livetex-statistics?site=true');

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




        /* выбираем нужные действия
            Действия влияют на сущность загружаемой статистики.
            Может быть статистика оператора (или -ов), чата (-ов), сайтов
        */
        if (!empty( $this->operId )) {
            $actions[] = 'oneOperator';
        }

        if ( $request->get('chat') ) {
            $actions[] = 'chat';
        }

        if ( $request->get('site') ) {
            $actions[] = 'site';
        }

        if (empty($actions)) {
            $actions[] = 'site';
            $actions[] = 'allOperators';
        }



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



        // получим статистику сайтов:
        if (in_array('site',$actions)) {
            $content['site'] = $API->testmethod('Site.GetList');
        }



        // Получим статистику выбранного оператора
        if (in_array('oneOperator',$actions)) {
            $oneOperator = $API->testmethod('Operator.ChatStat', [
                'date_begin' => $this->date_begin,
                'date_end' => $this->date_end,
                'operator_id' => $this->operId,
                'site_id' => $this->siteId,
            ]);

            if ($oneOperator)
                $content['oneOperator'] = $oneOperator;

            $this->l($oneOperator, 'oneOperator'); // Временный метод, используется для вывода отладочной инфы
        }



        if (in_array('allOperators',$actions)) {
            $content['allOperators'] = $API->method('Operator.GetList');

            $operators = $API->method('Operator.GetList');
            $operators_count_html =
                (isset($operators->response) and $operators->response)
                    ? count($operators->response)
                    : '*NOT_SET*';

            $page->setParam('operators', $operators);
            $page->setParam('operators_count_html', $operators_count_html);
        }


        $page->setParam('content', $content);
        $page->setParam('actions', $actions);
        $page->setParam('aside_menu', $this->aside_menu);

        $stat_params['date_begin'] = [ 'name' => 'date_begin', 'value' => ($this->date_begin) ?: '', 'descr' => 'Дата начала'];
        $stat_params['date_end'] = [ 'name' => 'date_end', 'value' => ($this->date_end) ?: '', 'descr' => 'Дата окончания'];
        $stat_params['operId'] = [ 'name' => 'operId', 'value' => ($this->operId) ?: $this->operId, 'descr' => 'Идентификатор оператора'];
        $stat_params['chatId'] = [ 'name' => 'chatId', 'value' => ($this->chatId) ?: '', 'descr' => 'Идентификатор чата'];
        $stat_params['siteId'] = [ 'name' => 'siteId', 'value' => ($this->siteId) ?: '', 'descr' => 'Идентификатор сайта'];
        $stat_params['actions'] = [ 'name' => 'actions', 'value' => implode('|',$actions), 'descr' => 'Сущности статистики'];


        $page->setParam('stat_params', $stat_params);
        //$page->setParam('date_begin', $this->date_begin);
        //$page->setParam('date_end', $this->date_end);

        return new \Http\Response( $page->show() );
    }




    // for sidebar menu BEGIN
    /*
     * Методы для работы с меню статистики (отображается в правом сайдбаре)
     */
    public function addMenu($name, $link){
        if (!empty($name))
            if (!empty($link))
                return $this->aside_menu[] = ['name' => $name, 'link' => $link ];

        return false;
    }

    public function setMenu($name, $link){
        if (!empty($name))
            if (!empty($link))
                return  $this->aside_menu[$name] = $link;

        return false;
    }

    public function getMenu($name){
        if (!empty($name))
            if ( isset( $this->aside_menu[$name] ) )
                return $this->aside_menu[$name];

        return false;
    }

    // for sidebar menu END




    // Временный метод, используется для вывода отладочной инфы
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



    /*
     * Обнулим/переопределим некоторые ненужные на странице статистики функции из родительского класса,
     * чтобы немного быстрее страница грузилась
     */
    public function slotMainMenu() { return ''; }
    public function slotUserbar() { return ''; }
    public function slotMyThings() { return ''; }

}