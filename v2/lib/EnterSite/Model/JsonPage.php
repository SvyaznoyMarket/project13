<?php

namespace EnterSite\Model {
    class JsonPage extends Page {
        public function __construct() {
            $this->path = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : null;
        }
    }
}