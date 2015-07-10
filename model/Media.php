<?php
namespace Model;

use Model\Media\Source;

class Media {

    const PROVIDER_IMAGE = 'image';
    const PROVIDER_FILE = 'file';
    const TYPE_ORIGINAL = 'original';

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

    /** Возвращает первый source с заданным тэгом
     * @param $tag
     * @return Source|null
     */
    public function getFirstSourceWithTag($tag) {
        foreach ($this->sources as $source) {
            if (in_array($tag, $this->tags)) {
                return $source;
            }
        }
        return null;
    }

    /** Является ли медиа файловым вложением
     * @return bool
     */
    public function isFile() {
        return $this->provider === self::PROVIDER_FILE;
    }

    /** Является ли медиа изображением
     * @return bool
     */
    public function isImage() {
        return $this->provider === self::PROVIDER_IMAGE;
    }

    /** Ссылка на файл (если это файловое вложение)
     * @return null|string
     */
    public function getFileLink() {
        return $this->isFile() && $this->sources ? $this->sources[0]->url : null;
    }

    /** Ссылка на оригинальное изображение
     * @return null|string
     */
    public function getOriginalImage() {
        return $this->getSource(self::TYPE_ORIGINAL) ? $this->getSource(self::TYPE_ORIGINAL)->url : null;
    }

}