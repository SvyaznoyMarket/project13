<?php

class ExceptionStack {
    /** @var \Exception[] */
    private $exceptions = [];

    /**
     * @param Exception $e
     */
    public function add(\Exception $e) {
        $this->exceptions[] = $e;
    }

    /**
     * @param Exception $e
     */
    public function remove(\Exception $e) {
        if (false !== $i = array_search($e, $this->exceptions)) {
            unset($this->exceptions[$i]);
        }
    }

    /**
     * @return Exception[]
     */
    public function all() {
        return $this->exceptions;
    }

    /**
     * @return Exception|null
     */
    public function first() {
        $first = reset($this->exceptions);
        if ($first) {
            return $first;
        } else {
            return null;
        }
    }
    
    /**
     * @return Exception|null
     */
    public function last() {
        $last = end($this->exceptions);
        reset($this->exceptions);
        if ($last) {
            return $last;
        } else {
            return null;
        }
    }
}