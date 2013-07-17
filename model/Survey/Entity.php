<?php

namespace Model\Survey;

class Entity {
    /** @var bool */
    private $isActive;
    /** @var string[] */
    private $regionNames;
    /** @var string */
    private $question;
    /** @var string[] */
    private $answers;
    /** @var int */
    private $showDelay;
    /** @var string */
    private $outputFile;

    public function __construct(array $data = []) {
        if (array_key_exists('active', $data)) $this->setIsActive($data['active']);
        if (array_key_exists('region_names', $data)) $this->setRegionNames($data['region_names']);
        if (array_key_exists('question', $data)) $this->setQuestion($data['question']);
        if (array_key_exists('answers', $data)) $this->setAnswers($data['answers']);
        if (array_key_exists('show_delay', $data)) $this->setShowDelay($data['show_delay']);
        if (array_key_exists('output_file', $data)) $this->setOutputFile($data['output_file']);
    }

    public function setIsActive($isActive) {
        $this->isActive = (bool)$isActive;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setRegionNames($regionNames) {
        $this->regionNames = $regionNames;
    }

    public function getRegionNames() {
        return $this->regionNames;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setAnswers($answers) {
        $this->answers = $answers;
    }

    public function getAnswers() {
        return $this->answers;
    }

    public function setShowDelay($showDelay) {
        $this->showDelay = (int)$showDelay;
    }

    public function getShowDelay() {
        return $this->showDelay;
    }

    public function setOutputFile($outputFile) {
        $this->outputFile = $outputFile;
    }

    public function getOutputFile() {
        return \App::config()->surveyDir . '/' . $this->outputFile;
    }

}