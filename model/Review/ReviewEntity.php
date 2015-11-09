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
    /** @var int Оценка пользователя */
    public $userVote = 0;
    /** @var bool */
    public $isMostHelpful;
    /** @var string|null Номер карты много.ру, с которым был оставлен отзыв */
    protected $mnogoru;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (isset($data['uid'])) $this->ui = $data['uid'];
        if (isset($data['origin'])) $this->origin = $data['origin'];
        if (isset($data['source_name'])) $this->sourceName = $data['source_name'];
        if (isset($data['source_logo_url'])) $this->sourceLogoUrl = $data['source_logo_url'];
        if (isset($data['url'])) $this->sourceUrl = $data['url'];
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['extract'])) $this->extract = $data['extract'];
        if (isset($data['pros'])) $this->pros = $data['pros'];
        if (isset($data['cons'])) $this->cons = $data['cons'];
        if (isset($data['author']) && !empty($data['author'])) $this->author = $data['author'];
        if (isset($data['score'])) $this->score = $data['score'];
        if (isset($data['star_score'])) $this->scoreStar = $data['star_score'];
        if (isset($data['date'])) $this->date = new \DateTime($data['date']);
        if (isset($data['useful_count'])) $this->usefulCount = $data['useful_count'];
        if (isset($data['not_useful_count'])) $this->notUsefulCount = $data['not_useful_count'];
        if (isset($data['positive'])) $this->positive = $data['positive'];
        if (isset($data['negative'])) $this->negative = $data['negative'];
        if (isset($data['user_vote'])) $this->userVote = $data['user_vote'];
        if (isset($data['is_most_helpful'])) $this->isMostHelpful = (bool)$data['is_most_helpful'];
        if (isset($data['mnogoru'])) $this->mnogoru = $data['mnogoru'];
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
        return $this->origin === self::ORIGIN_ENTER;
    }

    /** Это отзыв от Яндекса?
     * @return bool
     */
    public function isYandexReview(){
        return $this->origin === self::ORIGIN_YANDEX;
    }

    /** Это отзыв с привязанной картой Много.Ру?
     * @return bool
     */
    public function isMnogoRuReview(){
        return !empty($this->mnogoru);
    }

}