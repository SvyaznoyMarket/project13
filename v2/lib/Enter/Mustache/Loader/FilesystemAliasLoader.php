<?php

namespace Enter\Mustache\Loader;

class FilesystemAliasLoader extends \Mustache_Loader_FilesystemLoader implements \Mustache_Loader_MutableLoader {
    private $aliases = [];

    public function __construct($baseDir, array $aliases = array()) {
        parent::__construct($baseDir);
        $this->setTemplates($aliases);
    }

    public function load($name) {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        return parent::load($name);
    }

    public function setTemplates(array $templates) {
        $this->aliases = $templates;
    }

    public function setTemplate($name, $template) {
        $this->aliases[$name] = $template;
    }
}