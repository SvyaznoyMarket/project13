<?php

namespace Enter\Logging;

interface AppenderInterface {
    public function dump(array $messages);
}
