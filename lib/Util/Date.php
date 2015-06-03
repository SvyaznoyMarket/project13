<?php

namespace Util;

class Date {
    public static function strftimeRu($format, $timestamp) {
        if (false !== strpos($format, '%B2')) {
            $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
            $format = str_replace('%B2', $months[date('n', $timestamp) - 1], $format);
        }

        return strftime($format, $timestamp);
    }

    /** Проверка на соотвествие строке даты-времени
     * @param $str
     * @return bool
     */
    public static function isDateTimeString($str) {
        return preg_match('/^\d{4}-\d{2}-\d{2}.\d{2}:\d{2}:\d{2}?(\+\d{4})$/', $str) === 1;
    }
}