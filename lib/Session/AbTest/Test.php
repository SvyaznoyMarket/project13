<?php

namespace Session\AbTest;

class Test {

    /** @var string */
    private $key;

    /** @var bool */
    private $enabled;

    /** @var string */
    private $expireDate;

    /** @var TestCase[] */
    private $cases = [];

    /** @var TestCase|null */
    private $chosenCase;

    /** @var string|null */
    public $name;

    /** Слот для customVar в GA
     *  Слот 1 занят под abTestJson, а слоты 3, 4, 5 заняты под нужды сотрудников отдела аналитики
     * @var int|null
     */
    public $gaSlotNumber;

    /** Scope для CustomVar в GA
     * @var int
     */
    public $gaSlotScope = 2;

    public function __construct(array $data = []) {
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (array_key_exists('ga_slot_number', $data) && !empty($data['ga_slot_number'])) $this->gaSlotNumber = (int)$data['ga_slot_number'];
        if (array_key_exists('ga_slot_scope', $data) && !empty($data['ga_slot_scope'])) $this->gaSlotScope = (int)$data['ga_slot_scope'];
        if (array_key_exists('token', $data)) $this->key = $data['token'];
        if (array_key_exists('active', $data)) $this->enabled = $data['active'];
        if (array_key_exists('expires_at', $data)) $this->expireDate = $data['expires_at'];
        if (array_key_exists('cases', $data)) {
            foreach ($data['cases'] as $caseData) {
                $case = new TestCase($caseData);
                $this->cases[$case->getKey()] = $case;
            }
        }

        if (!isset($this->cases['default'])) {
            $this->cases['default'] = $this->getDefaultCase();
        }

        if (!$this->chosenCase) {
            $this->chosenCase = reset($this->cases);
        }
    }

    /**
     * @return TestCase
     */
    private function getDefaultCase() {
        return new TestCase([
            'token'   => 'default',
            'name'    => 'пусто',
            'traffic' => '*',
        ]);
    }

    /**
     * @return string|null
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getExpireDate() {
        return $this->expireDate;
    }

    /**
     * @return TestCase[]
     */
    public function getCases() {
        return $this->cases;
    }

    /**
     * @return TestCase|null
     */
    public function getChosenCase() {
        return $this->chosenCase;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->getEnabled() && strtotime($this->getExpireDate()) > time();
    }

    /** Выбор варианта для АБ-теста
     * @param string|null $chosenCase
     */
    public function chooseCase($chosenCase = null) {
        if (!$this->isActive()) {
            $this->chosenCase = $this->cases['default'];
            return;
        }

        if (isset($this->cases[$chosenCase])) {
            $this->chosenCase = $this->cases[$chosenCase];
            return;
        }

        $luck = mt_rand(0, 99);
        $total = 0;

        foreach ($this->cases as $case) {
            if ($total >= 100) continue;

            $diff = ($case->getTraffic() !== '*') ? (int)$case->getTraffic() : (100 - $total);
            if ($luck < $total + $diff) {
                $this->chosenCase = $case;
                return;
            }

            $total += $diff;
        }
    }
}
