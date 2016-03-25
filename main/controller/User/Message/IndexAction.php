<?php


namespace Controller\User\Message;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $config = \App::config();
        $curl = $this->getCurl();

        // настройки из cms
        /** @var Query\Config\GetByKeys|null $configQuery */
        $configQuery =
            $config->userCallback['enabled']
            ? (new Query\Config\GetByKeys(['site_call_phrases']))->prepare()
            : null
        ;

        $curl->execute();

        // SITE-6622
        $callbackPhrases = [];
        if ($configQuery) {
            foreach ($configQuery->response->keys as $item) {
                if ('site_call_phrases' === $item['key']) {
                    $value = json_decode($item['value'], true);
                    $callbackPhrases = !empty($value['private']) ? $value['private'] : [];
                }
            }
        }

        $page = new \View\User\Message\IndexPage();
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }
}