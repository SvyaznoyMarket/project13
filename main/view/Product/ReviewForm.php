<?php

namespace View\Product;

class ReviewForm {
    /**
     * Достоинства
     * @var string
     */
    private $pros;
    /**
     * Недостатки
     * @var string
     */
    private $cons;
    /**
     * Текст отзыва
     * @var string
     */
    private $extract;
    /**
     * Оценка (1..10)
     * @var int
     */
    private $score;
    /**
     * Имя автора отзыва
     * @var string
     */
    private $author;
    /**
     * E-mail автора
     * @var string
     */
    private $authorEmail;
    /** @var array */
    private $errors = array(
        'global'   => null,
        'pros' => null,
        'cons' => null,
        'extract' => null,
        'score' => null,
        'author' => null,
        'author_email' => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('pros', $data)) $this->setPros($data['pros']);
        if (array_key_exists('cons', $data)) $this->setCons($data['cons']);
        if (array_key_exists('extract', $data)) $this->setExtract($data['extract']);
        if (array_key_exists('score', $data)) $this->setScore($data['score']);
        if (array_key_exists('author', $data)) $this->setAuthor($data['author']);
        if (array_key_exists('author_email', $data)) $this->setAuthorEmail($data['author_email']);
    }

    /**
     * @param string $pros
     */
    public function setPros($pros) {
        $this->pros = trim((string)$pros);
    }

    /**
     * @return string
     */
    public function getPros() {
        return $this->pros;
    }

    /**
     * @param string $cons
     */
    public function setCons($cons) {
        $this->cons = trim((string)$cons);
    }

    /**
     * @return string
     */
    public function getCons() {
        return $this->cons;
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
     * @param int $score
     */
    public function setScore($score) {
        $this->score = (string)$score;
    }

    /**
     * @return int
     */
    public function getScore() {
        return $this->score;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = trim((string)$author);
    }

    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
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