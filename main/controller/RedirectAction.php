<?php

namespace Controller;

class RedirectAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        // проверка списка редиректов и переход
        $current_domain = $_SERVER['HTTP_HOST'] ?: $_SERVER['SERVER_NAME'];
        $current_link_full = $_SERVER['REQUEST_URI'];

        // на всякий случай, проверим/уберём "www":
        $current_domain = str_replace( 'www', '', $current_domain ) ;
        $current_link_full = str_replace( 'www'.$current_domain, $current_domain , $current_link_full ) ;


        $current_uri = str_replace( $current_domain, '', $current_link_full ) ;
        if ( strlen($current_uri)>1 and substr($current_uri, -1) == '/' ) $current_uri = substr($current_uri, 0, -1);


        if (
            method_exists( \App::curl(), 'query' ) and
            isset( \App::config()->dataStore['url'] )
        ) {

            // получим список урлов для редиректа
            $result = \App::curl()->query( \App::config()->dataStore['url'] . '301.json');

            if ( isset($result[$current_uri]) and !empty($result[$current_uri]) ) {
                // если нужно — go to *reidrect* :
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $result[$current_uri]);
                //header('Location: ' . "//" . $current_domain . '/' . $result[$current_uri]);
                exit();
            }

        }
    }
}
