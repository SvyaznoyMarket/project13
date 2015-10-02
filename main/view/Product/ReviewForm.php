<?php

namespace View\Product;

class ReviewForm {

    /**
     * Достоинства
     * @var string
     */
    private $advantage;

    /**
     * Недостатки
     * @var string
     */
    private $disadvantage;

    /**
     * Текст отзыва
     * @var string
     */
    private $extract;

    /**
     * Оценка (1..10)
     * @var float
     */
    private $score;

    /**
     * Имя автора отзыва
     * @var string
     */
    private $authorName;

    /**
     * E-mail автора
     * @var string
     */
    private $authorEmail;

    /**
     * Дата в формате "Y-m-d"
     * @var string
     */
    private $date;

    /**
     * Номер карты mnogo.ru
     *
     * @var string
     */
    private $mnogoru;

    /** @var array */
    private $errors = array(
        'global'   => null,
        'advantage' => null,
        'disadvantage' => null,
        'extract' => null,
        'score' => null,
        'author_name' => null,
        'author_email' => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('advantage', $data)) $this->setAdvantage($data['advantage']);
        if (array_key_exists('disadvantage', $data)) $this->setDisadvantage($data['disadvantage']);
        if (array_key_exists('extract', $data)) $this->setExtract($data['extract']);
        if (array_key_exists('score', $data)) $this->setScore($data['score']);
        if (array_key_exists('author_name', $data)) $this->setAuthorName($data['author_name']);
        if (array_key_exists('author_email', $data)) $this->setAuthorEmail($data['author_email']);
        if (array_key_exists('date', $data)) {
            $this->setDate($data['date']);
        } else {
            $this->setDate(date('Y-m-d'));
        }
        if (array_key_exists('mnogoru', $data)) $this->setMnogoRu($data['mnogoru']);

    }

    /**
     * @param $data
     */
    public function setMnogoRu($data) {
        $this->mnogoru = $data;
    }

    /**
     * @return string
     */
    public function getMnogoRu() {
        return $this->mnogoru;
    }

    /**
     * Возвращает значение, состоящее из одних цифр
     * @return string
     */
    public function getMnogoRuAsNumeric(){
        return preg_replace('@[^0-9]@', '', $this->mnogoru);
    }

    /**
     * @param string $advantage
     */
    public function setAdvantage($advantage) {
        $this->advantage = trim((string)$advantage);
    }

    /**
     * @return string
     */
    public function getAdvantage() {
        return $this->advantage;
    }

    /**
     * @param string $disadvantage
     */
    public function setDisadvantage($disadvantage) {
        $this->disadvantage = trim((string)$disadvantage);
    }

    /**
     * @return string
     */
    public function getDisadvantage() {
        return $this->disadvantage;
    }

    /**
     * @param string $extract
     */
    public function setExtract($extract) {
        $this->extract = trim((string)$extract);
    }

    /**
     * @return string
     */
    public function getExtract() {
        return $this->extract;
    }

    /**
     * @param float $score
     */
    public function setScore($score) {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore() {
        return $this->score;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName($authorName) {
        $this->authorName = trim((string)$authorName);
    }

    /**
     * @return string
     */
    public function getAuthorName() {
        return $this->authorName;
    }

    /**
     * @param string $aEmail
     */
    public function setAuthorEmail($aEmail) {
        $this->authorEmail = trim((string)$aEmail);
    }

    /**
     * @return string
     */
    public function getAuthorEmail() {
        return $this->authorEmail;
    }

    /**
     * @param string $date
     */
    public function setDate($date) {
        $this->date = trim((string)$date);
    }

    /**
     * @return string
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}