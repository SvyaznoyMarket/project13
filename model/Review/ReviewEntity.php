<?php

namespace Model\Review;


class ReviewEntity {

    const ORIGIN_ENTER = 'enter';
    const ORIGIN_YANDEX = 'yandex';

    /** @var string UID отзыва в SCMS */
    public $ui;
    /** @var string Источник отзыва (токен) */
    public $origin;
    /** @var string Источник отзыва (наименование) */
    public $sourceName;
    /** @var string URL логотипа источника отзыва */
    public $sourceLogoUrl;
    /** @var string Ссылка на отзыв в системе-источнике */
    public $sourceUrl;
    /** @var string Заголовок */
    public $title;
    /** @var string Основной текст */
    public $extract;
    /** @var string Текст о достоинствах */
    public $pros;
    /** @var string Текст о недостатках */
    public $cons;
    /** @var string Автор отзыва */
    public $author;
    /** @var int Оценка по 10-балльной шкале */
    public $score;
    /** @var int Оценка по 5-балльной шкале */
    public $scoreStar;
    /** @var \DateTime Дата публикации отзыва */
    public $date;
    /** @var int Количество неизвестных пользователей посчитавших этот отзыв полезным */
    public $usefulCount;
    /** @var int Количество неизвестных пользователей посчитавших этот отзыв бесполезным */
    public $notUsefulCount;
    /** @var int Количество авторизованных пользователей посчитавших этот отзыв полезным */
    public $positive;
    /** @var int Количество авторизованных пользователей посчитавших этот отзыв бесполезным */
    public $negative;

    public function __construct($arr) {
        if (isset($arr['uid'])) $this->ui = $arr['uid'];
        if (isset($arr['origin'])) $this->origin = $arr['origin'];
        if (isset($arr['source_name'])) $this->sourceName = $arr['source_name'];
        if (isset($arr['source_logo_url'])) $this->sourceLogoUrl = $arr['source_logo_url'];
        if (isset($arr['url'])) $this->sourceUrl = $arr['url'];
        if (isset($arr['title'])) $this->title = $arr['title'];
        if (isset($arr['extract'])) $this->extract = $arr['extract'];
        if (isset($arr['pros'])) $this->pros = $arr['pros'];
        if (isset($arr['cons'])) $this->cons = $arr['cons'];
        if (isset($arr['author']) && !empty($arr['author'])) $this->author = $arr['author'];
        if (isset($arr['score'])) $this->score = $arr['score'];
        if (isset($arr['score_star'])) $this->scoreStar = $arr['score_star'];
        if (isset($arr['date'])) $this->date = new \DateTime($arr['date']);
        if (isset($arr['useful_count'])) $this->usefulCount = $arr['useful_count'];
        if (isset($arr['not_useful_count'])) $this->notUsefulCount = $arr['not_useful_count'];
        if (isset($arr['positive'])) $this->positive = $arr['positive'];
        if (isset($arr['negative'])) $this->negative = $arr['negative'];
    }

    /** Положительная полезность отзыва
     * @return int
     */
    public function getPositiveCount(){
        return $this->usefulCount + $this->positive;
    }

    /** Отрицательная полезность отзыва
     * @return int
     */
    public function getNegativeCount(){
        return $this->notUsefulCount + $this->negative;
    }

    /** Это наш отзыв?
     * @return bool
     */
    public function isEnterReview(){
        return $this->origin == self::ORIGIN_ENTER;
    }

    /** Это отзыв от Яндекса?
     * @return bool
     */
    public function isYandexReview(){
        return $this->origin == self::ORIGIN_YANDEX;
    }

}