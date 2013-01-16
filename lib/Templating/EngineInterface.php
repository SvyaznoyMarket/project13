<?php

namespace Templating;

interface EngineInterface {
    public function render($source, array $params = array());
}
