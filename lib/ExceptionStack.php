<?php

class ExceptionStack {
    /** @var \Exception[] */
    private $exceptions = array();

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
}