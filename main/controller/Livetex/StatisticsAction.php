<?php

namespace Controller\Livetex;

class StatisticsAction {
    private $API;
    private $date_format = 'Y-m-d';
    private $date_begin = '2013-07-15';
    private $date_end = '2013-07-23';
    private $operId = null;
    private $chatId = null;
    private $actions = [];
    private $content = [];
    private $siteId = 41836; // TODO: move in config
    private $aside_menu = [];



    public function __construct() {
        include_once '../lib/LiveTex/API.php';
        $API = &$this->API;
        $API = \LiveTex\Api::getInstance(); //LiveTex/API
    }


    private function init(&$request){
        $operId = $request->get('operId');
        if ($operId) $this->operId = $operId;


        $siteId = $request->get('siteId');
        if ($siteId) $this->siteId = $siteId;

        $chatId = $request->get('chatId');
        if ($chatId) $this->chatId = $chatId;


        $actions_get = $request->get('actions');
        $this->actions = [];
        if ( !empty($actions_get) ) {
            $actions = explode('|',$actions_get);
            if ($actions and is_array($actions) ) $this->actions = $actions;
        }


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

        //$this->date_begin = '2013-05-15'; // tmp for debug
        //$this->date_end = '2013-07-25';

        $this->addMenu('Общая статистика', '/livetex-statistics');
        $this->addMenu('Статистика чатов', '/livetex-statistics?actions=chat');
        $this->addMenu('Статистика операторов', '/livetex-statistics?actions=allOperators');
        $this->addMenu('Статистика сайтов', '/livetex-statistics?actions=site');
        $this->addMenu('[ Главная страница сайта ]', '/');

    }



    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $page = new \View\Livetex\StatisticsPage();
        $actions = &$this->actions;
        $content = &$this->content;
        $API = &$this->API;                

        //$router = \App::router();
        //$client = \App::coreClientV2();
        //$user = \App::user(); // TODO: проверка на авторизацию и наличие прав на просмотр данного раздела
        //$region = $user->getRegion();

        $this->init($request);
        $this->selectActions($request);

        /* загружаем список операторов в любом случае (чтобы обращаться к нему в случае надобности) ... */
        $operators = $API->method('Operator.GetList');

        $operators_list = []; // отформатируем список операторов, чтобы обращаться к ним как $operators_list[$ID]
        if ( isset( $operators->response) ) {
            foreach( $operators->response as $item) {
                if ( isset($item->id) ) $operators_list[$item->id] = $item;
            }
        }

        $operators_count_html = // Количество операторов
            (isset($operators->response) and $operators->response)
                ? count($operators->response)
                : '*NOT_SET*';

        
        /* ... но отображаем список операторов в контенте только, если нужно есть соответствующий action в $actions */
        if (in_array('allOperators',$actions)) {
            $this->actionsAllOperators($operators);
        }        

        // получим статистику чата, если нужно:
        if (in_array('chat',$actions)) {
            $this->actionsChat();
        }

        // получим статистику сайтов:
        if (in_array('site',$actions)) {
            $this->actionsSite();
        }

        // Получим статистику выбранного оператора
        if (in_array('oneOperator',$actions)) {
            $this->actionsOneOperator();
        }

        // Получим общую статистику
        if (in_array('general',$actions)) {
            $this->actionsGeneral();
        }


        /*
         * $stat_params — Для наполнения формы сайдбара 
         */
        $stat_params['date_begin'] = [ 'value' => ($this->date_begin) ?: '', 'descr' => 'Дата начала'];
        $stat_params['date_end'] = [ 'value' => ($this->date_end) ?: '', 'descr' => 'Дата окончания'];
        $stat_params['operId'] = [ 'value' => ($this->operId) ?: $this->operId, 'descr' => 'Идентификатор оператора'];
        $stat_params['chatId'] = [ 'value' => ($this->chatId) ?: '', 'descr' => 'Идентификатор чата'];
        $stat_params['siteId'] = [ 'value' => ($this->siteId) ?: '', 'descr' => 'Идентификатор сайта'];
        $stat_params['actions'] = [ 'value' => implode('|',$actions), 'descr' => 'Сущности статистики'];

        $page->setParam('stat_params', $stat_params);
        $page->setParam('operators', $operators);
        $page->setParam('operators_list', $operators_list);
        $page->setParam('operators_count_html', $operators_count_html);
        $page->setParam('content', $content);
        $page->setParam('actions', $actions);
        $page->setParam('aside_menu', $this->aside_menu);

        return new \Http\Response( $page->show() );
    }


    
    
    private function actionsChat() {
        $content = &$this->content;
        $API = &$this->API;
        
        if ( !empty($this->operId) ) {
            $operator_chat = $API->testmethod('Operator.ChatStat', [
                'date_begin' => $this->date_begin,
                'date_end' => $this->date_end,
                'operator_id' => $this->operId,
            ]);
            //$this->l($operator_chat, '$operator_chat');
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
    
    
    
    private function actionsSite() {
        $API = &$this->API;
        $content = &$this->content;
        $content['site'] = $API->testmethod('Site.GetList');
    }


    private function actionsGeneral() {
        $API = &$this->API;
        $content = &$this->content;

        $content['general'] = $API->testmethod('Site.ChatHistory', [
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'site_id' => $this->siteId,
        ]);

        $this->l( $content['general'] );
    }
    
    
    
    private function actionsOneOperator() {
        $API = &$this->API; $content = &$this->content;
        $oneOperator = $API->testmethod('Operator.ChatStat', [
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'operator_id' => $this->operId,
            'site_id' => $this->siteId,
        ]);

        if ($oneOperator)
            $content['oneOperator'] = $oneOperator;

        //$this->l($oneOperator, 'oneOperator'); // Временный метод, используется для вывода отладочной инфы
    }
    
    
    
    private function actionsAllOperators($operators) {
        $content = &$this->content;
        $content['allOperators'] = $operators;
    }

    
    

    /**
     * выбираем нужные действия
     * Действия влияют на сущность загружаемой статистики.
     * Может быть статистика оператора (или -ов), чата (-ов), сайтов
     *
     * @param $request
     */
    private function selectActions($request) {
        $actions = &$this->actions;

        if (!empty( $this->operId )) {
            $actions[] = 'oneOperator';
        }

        if ( $request->get('chat') ) {
            $actions[] = 'chat';
        }

        if ( $request->get('site') ) {
            $actions[] = 'site';
        }

        if ( empty($actions) ) {
            $actions[] = 'general';
            //$actions[] = 'site';
            //$actions[] = 'allOperators';
        }

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





    /*
     * Временный метод, используется для вывода отладочной инфы
     */
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


}