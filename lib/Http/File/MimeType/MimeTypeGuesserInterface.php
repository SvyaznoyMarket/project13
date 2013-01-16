<?php

namespace Http\File\MimeType;

use Http\File\Exception\FileNotFoundException;
use Http\File\Exception\AccessDeniedException;

/**
 * Guesses the mime type of a file
 */
interface MimeTypeGuesserInterface {
    /**
     * Guesses the mime type of the file with the given path.
     *
     * @param string $path The path to the file
     *
     * @return string         The mime type or NULL, if none could be guessed
     *
     * @throws FileNotFoundException  If the file does not exist
     * @throws AccessDeniedException  If the file could not be read
     */
    public function guess($path);
}
