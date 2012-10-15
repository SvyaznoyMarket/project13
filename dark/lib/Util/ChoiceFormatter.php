<?php

namespace Util;

/**
 * ChoiceFormatter converts between ranges of numeric values and string
 * names for those ranges.
 *
 * A ChoiceFormatter splits the real number line -Inf to +Inf into two or
 * more contiguous ranges. Each range is mapped to a string.
 * ChoiceFormatter is generally used in a MessageFormat for displaying
 * grammatically correct plurals such as "There are 2 files."
 *
 * <code>
 *  $string = '[0] are no files |[1] is one file |(1,Inf] are {number} files';
 *
 *  $translated = $formatter->format($string);
 *
 *  $choice = new ChoiceFormatter();
 *  echo $choice->format($translated, 0); //shows "are no files"
 * </code>
 *
 * The message/string choices are separated by the pipe "|" followed
 * by a set notation of the form
 *  # <t>[1,2]</t> -- accepts values between 1 and 2, inclusive.
 *  # <t>(1,2)</t> -- accepts values between 1 and 2, excluding 1 and 2.
 *  # <t>{1,2,3,4}</t> -- only values defined in the set are accepted.
 *  # <t>[-Inf,0)</t> -- accepts value greater or equal to negative infinity
 *                       and strictly less than 0
 * Any non-empty combinations of the delimiters of square and round brackets
 * are acceptable.
 */
class ChoiceFormatter {
    /**
     * The pattern to validate a set notation
     */
    protected $validate = '/[\(\[\{]|[-Inf\d:\s]+|,|[\+Inf\d\s:\?\-=!><%\|&\(\)]+|[\)\]\}]/ms';

    /**
     * The pattern to parse the formatting string.
     */
    protected $parse = '/\s*\|?([\(\[\{]([-Inf\d:\s]+,?[\+Inf\d\s:\?\-=!><%\|&\(\)]*)+[\)\]\}])\s*/';

    /**
     * The value for positive infinity.
     */
    protected $inf;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->inf = -log(0);
    }

    /**
     * Determines if the given number belongs to a given set
     *
     * @param  float  $number the number to test.
     * @param  string $set    the set, in set notation.
     * @return boolean true if number is in the set, false otherwise.
     */
    public function isValid($number, $set) {
        $n = preg_match_all($this->validate, $set, $matches, PREG_SET_ORDER);

        if ($n < 3) {
            throw new \Exception(sprintf('Invalid set "%s".', $set));
        }

        if (preg_match('/\{\s*n:([^\}]+)\}/', $set, $def)) {
            return $this->isValidSetNotation($number, $def[1]);
        }

        $leftBracket = $matches[0][0];
        $rightBracket = $matches[$n - 1][0];

        $i = 0;
        $elements = array();

        foreach ($matches as $match) {
            $string = $match[0];
            if ($i != 0 && $i != $n - 1 && $string !== ',') {
                if ($string == '-Inf') {
                    $elements[] = -1 * $this->inf;
                } else if ($string == '+Inf' || $string == 'Inf') {
                    $elements[] = $this->inf;
                } else {
                    $elements[] = floatval($string);
                }
            }
            $i++;
        }
        $total = count($elements);
        $number = floatval($number);

        if ($leftBracket == '{' && $rightBracket == '}') {
            return in_array($number, $elements);
        }

        $left = false;
        if ($leftBracket == '[') {
            $left = $number >= $elements[0];
        } else if ($leftBracket == '(') {
            $left = $number > $elements[0];
        }

        $right = false;
        if ($rightBracket == ']') {
            $right = $number <= $elements[$total - 1];
        } else if ($rightBracket == ')') {
            $right = $number < $elements[$total - 1];
        }

        if ($left && $right) {
            return true;
        }

        return false;
    }

    protected function isValidSetNotation($number, $set) {
        $str = '$result = ' . str_replace('n', '$number', $set) . ';';
        try {
            eval($str);

            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parses a choice string and get a list of sets and a list of strings corresponding to the sets.
     *
     * @param  string $string the string containing the choices
     * @return array array($sets, $strings)
     */
    public function parse($string) {
        $n = preg_match_all($this->parse, $string, $matches, PREG_OFFSET_CAPTURE);
        $sets = array();
        foreach ($matches[1] as $match) {
            $sets[] = $match[0];
        }

        $offset = $matches[0];
        $strings = array();
        for ($i = 0; $i < $n; $i++) {
            $len = strlen($offset[$i][0]);
            $begin = $i == 0 ? $len : $offset[$i][1] + $len;
            $end = $i == $n - 1 ? strlen($string) : $offset[$i + 1][1];
            $strings[] = substr($string, $begin, $end - $begin);
        }

        return array($sets, $strings);
    }

    /**
     * For the choice string, and a number, find and return the string that satisfied the set within the choices.
     *
     * @param  string $string   the choices string.
     * @param  float  $number   the number to test.
     * @return string the choosen string.
     */
    public function format($string, $number) {
        list($sets, $strings) = $this->parse($string);
        $total = count($sets);
        for ($i = 0; $i < $total; $i++) {
            if ($this->isValid($number, $sets[$i])) {
                return $strings[$i];
            }
        }

        return false;
    }
}
