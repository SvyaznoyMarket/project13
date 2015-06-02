<?php
namespace Model;

use Model\Media\Source;

class Media {
    /** @var string|null */
    public $uid;
    /** @var string|null */
    public $name;
    /** @var string|null */
    public $contentType;
    /** @var string|null */
    public $provider;
    /** @var array */
    public $tags = [];
    /** @var Source[] */
    public $sources = [];

    public function __construct(array $data = []) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['content_type'])) $this->contentType = $data['content_type'];
        if (isset($data['provider'])) $this->provider = $data['provider'];
        if (isset($data['tags']) && is_array($data['tags'])) $this->tags = $data['tags'];

        if (isset($data['sources'][0])) {
            foreach ($data['sources'] as $source) {
                $this->sources[] = new Source($source);
            }
        }
    }

    /**
     * @param string $type
     * @return Source|null
     */
    public function getSource($type) {
        foreach ($this->sources as $source) {
            if ($source->type === $type) {
                return $source;
            }
        }

        return null;
    }
}