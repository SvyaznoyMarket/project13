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
    /** @var bool */
    private $isAnswered;
    /** @var datetime */
    private $initTime;
    /** @var int */
    private $expandedWidthDiff;
    /** @var int */
    private $expandedHeightDiff;

    public function __construct(array $data = []) {
        if (array_key_exists('active', $data)) $this->setIsActive($data['active']);
        if (array_key_exists('region_names', $data)) $this->setRegionNames($data['region_names']);
        if (array_key_exists('question', $data)) $this->setQuestion($data['question']);
        if (array_key_exists('answers', $data)) $this->setAnswers($data['answers']);
        if (array_key_exists('show_delay', $data)) $this->setShowDelay($data['show_delay']);
        if (array_key_exists('output_file', $data)) $this->setOutputFile($data['output_file']);
        if (array_key_exists('expanded_width_diff', $data)) $this->setExpandedWidthDiff($data['expanded_width_diff']);
        if (array_key_exists('expanded_height_diff', $data)) $this->setExpandedHeightDiff($data['expanded_height_diff']);

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

    public function setInitTime($timestamp) {
        $this->initTime = $timestamp;
    }

    public function getInitTime() {
        return $this->initTime;
    }

    public function setExpandedWidthDiff($expandedWidthDiff) {
        $this->expandedWidthDiff = (int)$expandedWidthDiff;
    }

    public function getExpandedWidthDiff() {
        return $this->expandedWidthDiff;
    }

    public function setExpandedHeightDiff($expandedHeightDiff) {
        $this->expandedHeightDiff = (int)$expandedHeightDiff;
    }

    public function getExpandedHeightDiff() {
        return $this->expandedHeightDiff;
    }

    public function getIsTimePassed() {
        if(empty($this->initTime) || empty($this->showDelay)) {
            return false;
        } else {
            return (new \DateTime())->getTimestamp() - $this->initTime->getTimestamp() > $this->showDelay;
        }
    }

    public function isAnswered($cookieInitTimeStamp) {
        return $cookieInitTimeStamp == $this->initTime->getTimestamp();
    }

}