<?php
/**
 * Created by PhpStorm.
 * User: rmn
 * Date: 13.04.15
 * Time: 14:15
 */

namespace Controller\Product;


use Controller\Error\NotFoundAction;
use Http\RedirectResponse;

/** Контроллер для товаров от Викимарта
 * Class WikimartAction
 * @package Controller\Product
 */
class WikimartAction {

    /** Редирект на страницу товара по Викимарт-ID товара
     *  URL для редиректа получается из SCMS
     * @param $wmProductId
     * @return RedirectResponse|\Http\Response
     */
    public function redirect($wmProductId){

        $scmsResponse = \App::scmsClient()->query('product/get-description/v1.json',
            ['wikimart_ids' => [$wmProductId], 'geo_id' => \App::user()->getRegionId()]);

        if (is_array($scmsResponse)
            && isset($scmsResponse['products'])
            && (bool)($scmsResponse['products'])
        ) {
            $product = reset($scmsResponse['products']);
            if (isset($product['url']) && $product['url']) {
                return new RedirectResponse($product['url']);
            }
        }

        return (new NotFoundAction())->execute(new \Exception('Fail'), \App::request());
    }

}