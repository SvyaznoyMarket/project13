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
        if (array_key_exists('gaSlotNumber', $data) && !empty($data['gaSlotNumber'])) $this->gaSlotNumber = (int)$data['gaSlotNumber'];
        if (array_key_exists('gaSlotScope', $data) && !empty($data['gaSlotScope'])) $this->gaSlotScope = (int)$data['gaSlotScope'];
        if (array_key_exists('key', $data)) $this->key = $data['key'];
        if (array_key_exists('enabled', $data)) $this->enabled = $data['enabled'];
        if (array_key_exists('expireDate', $data)) $this->expireDate = $data['expireDate'];
        if (array_key_exists('cases', $data)) {
            foreach ($data['cases'] as $caseKey => $caseData) {
                $case = new TestCase(array_merge($caseData, ['key' => $caseKey]));
                $this->cases[$case->getKey()] = $case;
            }
        }

        if (!isset($this->cases['default']))
            $this->cases['default'] = $this->getDefaultCase();
    }

    /**
     * @return TestCase
     */
    private function getDefaultCase() {
        return new TestCase([
            'key'      => 'default',
            'name'     => 'пусто',
            'traffic'  => '*',
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
