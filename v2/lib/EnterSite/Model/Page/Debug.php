<?php

namespace EnterSite\Model\Page {
    class Debug {
        /** @var string */
        public $requestId;
        /** @var string */
        public $path;
        /** @var string */
        public $name;
        /** @var string */
        public $route;
        /** @var Debug\Error|null */
        public $error;
        /** @var Debug\Git|null */
        public $git;
        /** @var Debug\Time[] */
        public $times = [];
        /** @var Debug\Memory|null */
        public $memory;
        /** @var array|null */
        public $session;
        /** @var array|null */
        public $config;
        /** @var Debug\Query[] */
        public $queries = [];

        public function __construct() {}
    }
}

namespace EnterSite\Model\Page\Debug {
    class Error {
        /** @var int */
        public $type;
        /** @var string */
        public $message;
        /** @var string */
        public $file;
        /** @var string */
        public $line;

        /**
         * @param $data
         */
        public function __construct($data = []) {
            if (array_key_exists('type', $data)) $this->type = $data['type'];
            if (array_key_exists('message', $data)) $this->message = $data['message'];
            if (array_key_exists('file', $data)) $this->file = $data['file'];
            if (array_key_exists('line', $data)) $this->line = $data['line'];
        }
    }

    class Git {
        /** @var string */
        public $branch;
        /** @var string */
        public $tag;
    }
}

namespace EnterSite\Model\Page\Debug {
    class Time {
        /** @var float */
        public $value;
        /** @var string */
        public $unit;
    }
}

namespace EnterSite\Model\Page\Debug {
    class Memory {
        /** @var float */
        public $value;
        /** @var string */
        public $unit;
    }
}

namespace EnterSite\Model\Page\Debug {
    use EnterSite\Model\Page\Debug;

    class Query {
        /** @var string */
        public $url;
        /** @var string */
        public $path;
        /** @var float */
        public $time;
        /** @var int */
        public $call;
        /** @var array */
        public $css = [
            'top'    => null,
            'left'   => null,
            'width1' => null,
            'color1' => null,
            'color2' => null,
        ];
        /** @var string */
        public $info;
        /** @var string */
        public $id;
        /** @var string */
        public $logId;
    }
}