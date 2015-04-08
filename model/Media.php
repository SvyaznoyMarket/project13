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

        if (isset($data['sources']) && is_array($data['sources'])) {
            foreach ($data['sources'] as $source) {
                if (is_array($source)) {
                    $this->sources[] = new Source($source);
                }
            }
        }
    }

    /**
     * @param string $type
     * @return Source|null
     */
    public function getSourceByType($type) {
        foreach ($this->sources as $source) {
            if ($source->type === $type) {
                return $source;
            }
        }

        return null;
    }

    /** Является ли медиа файловым вложением (не картинкой)
     * @return bool
     */
    public function isFile(){
        return $this->provider === 'file';
    }

    /** Ссылка на файл (если это файловое вложение)
     * @return null|string
     */
    public function getFileLink() {
        return $this->isFile() && $this->sources ? $this->sources[0]->url : null;
    }
}