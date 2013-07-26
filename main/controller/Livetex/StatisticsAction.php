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
    private $heads = [];
    private $siteId = 41836; // TODO: move in config
    private $aside_menu = [];
    private $operators_list = [];



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
        //$this->actions = [];
        if ( !empty($actions_get) ) {
            $actions = explode('|',$actions_get);
            if ($actions and is_array($actions) ) {
                $actions = array_unique($actions);
                $this->actions = $actions;
            }
        }

        //$this->l($this->actions, 'actions_get');


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
        $this->addMenu('Статистика чатов', '/livetex-statistics?actions=Chat');
        $this->addMenu('Статистика операторов', '/livetex-statistics?actions=AllOperators');
        $this->addMenu('Статистика сайтов', '/livetex-statistics?actions=Site');
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
        $this->actionsSelect($request);

        /* загружаем список операторов в любом случае (чтобы обращаться к нему в случае надобности) ... */
        $operators = $API->method('Operator.GetList');

        $operators_list = & $this->operators_list;
        $operators_list = []; // отформатируем список операторов, чтобы обращаться к ним как $operators_list[$ID]
        if ( isset( $operators->response) ) {
            foreach( $operators->response as $item) {
                if ( isset($item->id) ) $operators_list[$item->id] = $item;
            }
        }

        $operators_count_html = // Количество операторов
            (isset($operators->response) and $operators->response)
                ? count($operators->response) : '*NOT_SET*';

        
        /* ... но отображаем список операторов в контенте только, если нужно есть соответствующий action в $actions */
        if (in_array('AllOperators',$actions)) {
            $this->actionsAllOperators($operators);
        }        

        // получим статистику чата, если нужно:
        if (in_array('Chat',$actions)) {
            $this->actionsChat();
        }

        // получим статистику сайтов:
        if (in_array('Site',$actions)) {
            $this->actionsSite();
        }

        // Получим статистику выбранного оператора
        if (in_array('OneOperator',$actions)) {
            $this->actionsOneOperator();
        }

        // Получим общую статистику
        if (in_array('General',$actions)) {
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
        //$this->l($content,'content20');
        $page->setParam('content', $content);
        $page->setParam('heads', $this->heads);
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
            $content['OperatorChat'] = $operator_chat;
            $this->heads['OperatorChat']['big_head'] = 'LiveTex: Статистика чатов оператора';
        }


        if ( !empty($this->siteId) ) {
            $site_chat = $API->method('Site.ChatStat', [
                'date_begin' => $this->date_begin,
                'date_end' => $this->date_end,
                'site_id' => $this->siteId,
            ]);
            //$this->l($site_chat,'site_chat');
            $content['SiteChat'] = $site_chat;
            $this->heads['SiteChat']['big_head'] = 'LiveTex: Статистика чатов сайта';
        }

        $chat_active = $API->method('Chat.GetActive', []);

        $this->heads['ChatHistory']['big_head'] = 'LiveTex: Статистика истории чатов';
        $content['ChatHistory'] = $API->testmethod('Site.ChatHistory', [
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'site_id' => $this->siteId,
        ]);
        //$this->l($chat_active,'chat_active');

        $content['ChatActive'] = $chat_active;
        $this->heads['ChatActive']['big_head'] = 'LiveTex: Статистика активных чатов';
        //$this->l($chat_active,'chat_active');
    }
    
    
    
    private function actionsSite() {
        $API = &$this->API;
        $content = &$this->content;
        $content['Site'] = $API->testmethod('Site.GetList');
    }


    private function actionsGeneral() {
        $API = &$this->API;
        $content = &$this->content;

        $this->heads['General']['big_head'] = 'LiveTex: Общая статистика';
        $content['General'] = $API->testmethod('Site.ChatHistory', [
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'site_id' => $this->siteId,
        ]);

        //$this->l( $content['general'] );
    }


    /*
    * Формируем слот с html с инфой об выбранном операторе
    */
    private function actionsOneOperator() {
        $API = &$this->API;
        $content = &$this->content;
        $OneOperator = $API->testmethod('Operator.ChatStat', [
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'operator_id' => $this->operId,
            'site_id' => $this->siteId,
        ]);

        //$content['OperatorInfo'] = $operators_list[];

        if ($OneOperator) {
            $content['OneOperator'] = $OneOperator;
            $this->heads['OneOperator']['big_head'] = 'LiveTex: Cтатистика оператора';
        }
        //$this->l($oneOperator, 'oneOperator');
    }
    
    
    
    private function actionsAllOperators($operators) {
        $content = &$this->content;
        $content['AllOperators'] = $operators;
        $this->heads['AllOperators']['big_head'] = 'LiveTex: Cтатистика операторов';
    }

    
    

    /**
     * выбираем нужные действия
     * Действия влияют на сущность загружаемой статистики.
     * Может быть статистика оператора (или -ов), чата (-ов), сайтов
     *
     * @param $request
     */
    private function actionsSelect($request) {
        $actions = &$this->actions;
        //$actions = array_unique($actions);

        if (!empty( $this->operId )) {
            $actions[] = 'OneOperator';
        }

        if ( $request->get('chat') ) {
            $actions[] = 'Chat';
        }

        if ( $request->get('site') ) {
            $actions[] = 'Site';
        }

        if ( empty($actions) ) {
            $actions[] = 'General';
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