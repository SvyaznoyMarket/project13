<?php


namespace Session\AbTest;

/** Трейт, помогающий определять вариант АБ-теста у пользователя
 * Class ABHelperTrait
 * @package Session\AbTest
 */
trait ABHelperTrait {

    /** Новая главная страница?
     * @return bool
     */
    public function isNewMainPage() {
        return \App::abTest()->getTest('main_page') && \App::abTest()->getTest('main_page')->getChosenCase()->getKey() == 'new';
    }

} 