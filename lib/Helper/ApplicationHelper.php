<?php

namespace Helper;

class ApplicationHelper {

    public function getMethodValues($object, $out = 'file', $file = '/tmp/logger.txt') {
        $result = [];
        foreach (get_class_methods($object) as $method) {
            try {
                $result[] = "$method: " . $object->$method();
            } catch (\Exception $e) { }
        }
        switch ($out) {
            case 'file':
                file_put_contents($file, implode(PHP_EOL, $result), FILE_APPEND);
                break;
            case 'browser':
                $result = implode('<br>', $result);
                break;
            default:
                break;
        }
        return $result;
    }
}