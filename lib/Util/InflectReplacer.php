<?php

namespace Util;

class InflectReplacer {
    private $caseIndexes = [
        'и' => 0,
        'р' => 1,
        'д' => 2,
        'в' => 3,
        'т' => 4,
        'п' => 5,
    ];
    /** @var array */
    private $patterns = [];

    public function __construct(array $patterns) {
        $this->patterns = $patterns;
    }

    /**
     * @param string $value Например "Продажа {категория|р}: цены на новые модели {категория|р} в {город|п} с обзорами и отзывами, купить {категория|в} в {сайт|п}"
     * @return string Например "Продажа электроники: цены на новые модели электроники в Москве с обзорами и отзывами, купить  электронику в интернет-магазине Enter.ru"
     * @throws \Exception
     */
    public function get($value) {
        foreach ($this->patterns as $pattern => $replace) {
            $matches = [];
            if (preg_match('/{' . $pattern . '\|([а-я]+)}/ui', $value, $matches)) {
                $match = !empty($matches[0]) ? $matches[0] : null;
                $case = !empty($matches[1]) ? $matches[1] : null;
                if (!$case) {
                    throw new \Exception(sprintf('Не получено название падежа для %s', $match));
                }

                if (!isset($this->caseIndexes[$case])) {
                    throw new \Exception(sprintf('Неправильное обозначение падежа %s', $case));
                }
                $caseIndex = $this->caseIndexes[$case];
                if (empty($this->patterns[$pattern][$caseIndex])) {
                    \App::logger()->error(sprintf('Не найден падеж %s для %s', $case, $pattern));
                    $caseIndex = 0;
                }
                $replace = $replace[$caseIndex];

                $value = str_replace($match, $replace, $value);
            }
        }

        return $value;
    }
}