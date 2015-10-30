<?php
namespace Model;

class Medias implements \Iterator, \ArrayAccess, \Countable {
    /** @var Media[] */
    private $items = [];
    /** @var int */
    private $position = 0;

    /**
     * @param mixed $data
     */
    public function __construct($data = []) {
        if (is_array($data)) {
            foreach ($data as $media) {
                if (is_array($media)) {
                    $this->items[] = new Media($media);
                }
            }
        }
    }

    /**
     * @param string $sourceType
     * @param string $mediaProvider
     * @param string $mediaTag
     * @return Media\Source
     */
    public function getMediaSource($sourceType, $mediaTag = 'main', $mediaProvider = 'image') {
        foreach ($this->items as $media) {
            if ($media->provider === $mediaProvider && (!$mediaTag || in_array($mediaTag, $media->tags, true))) {
                foreach ($media->sources as $source) {
                    if ($source->type === $sourceType) {
                        return $source;
                    }
                }
            }
        }

        return new Media\Source();
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->items[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->items[$this->position]);
    }

    public function count() {
        return count($this->items);
    }
}