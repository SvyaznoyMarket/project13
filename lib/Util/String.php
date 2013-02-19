<?php

namespace Util;

class String {
    public static function ucfirst($value) {
        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1);
    }
}