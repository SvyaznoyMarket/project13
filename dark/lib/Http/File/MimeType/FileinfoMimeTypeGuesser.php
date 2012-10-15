<?php

namespace Http\File\MimeType;

use Http\File\Exception\FileNotFoundException;
use Http\File\Exception\AccessDeniedException;

/**
 * Guesses the mime type using the PECL extension FileInfo
 */
class FileinfoMimeTypeGuesser implements MimeTypeGuesserInterface {
    /**
     * Returns whether this guesser is supported on the current OS/PHP setup
     *
     * @return Boolean
     */
    public static function isSupported() {
        return function_exists('finfo_open');
    }

    public function guess($path) {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_readable($path)) {
            throw new AccessDeniedException($path);
        }

        if (!self::isSupported()) {
            return null;
        }

        if (!$finfo = new \finfo(FILEINFO_MIME_TYPE)) {
            return null;
        }

        return $finfo->file($path);
    }
}
