<?php

namespace Model\Banner;

use Model\Media;

class BannerEntity {

    const TYPE_PAGE = 'static-page';
    const TYPE_SLICE = 'slice';
    const TYPE_CATEGORY = 'category';
    const TYPE_LINK = 'url';

    const TAG_IMAGE_BIG = 'site_960x240';
    const TAG_IMAGE_SMALL = 'site_220x50';

    /** @var string */
    public $uid;
    /** @var string */
    private $type;
    /** @var string */
    public $name;
    /** @var Media[] */
    public $medias = [];
    /** @var string */
    public $url;
    /** @var \Model\Slice\Entity|null */
    public $slice;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('uid', $data)) $this->uid = $data['uid'];
        if (array_key_exists('type', $data)) $this->type = $data['type'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (isset($data['slice']['uid'])) $this->slice = new \Model\Slice\Entity($data['slice']);
        if (array_key_exists('medias', $data) && is_array($data['medias'])) {
            $this->medias = array_map(function($mediaData) { return new Media($mediaData); }, $data['medias']);
        }

        // Url из данных
        if ($this->isPage() && isset($data['static_page']['token'])) {
            $this->url = '/'. $data['static_page']['token'];
        } else if ($this->isCategory() && isset($data['category']['url'])) {
            $this->url = $data['category']['url'];
        } else if ($this->isSlice() && isset($data['slice']['url'])) {
            $this->url = $data['slice']['url'];
        } else if ($this->isLink() && !empty($data['url'])) {
            $this->url = $data['url'];
        }
    }

    public function isPage() {
        return $this->type == self::TYPE_PAGE;
    }

    public function isCategory() {
        return $this->type == self::TYPE_CATEGORY;
    }

    public function isSlice() {
        return $this->type == self::TYPE_SLICE;
    }

    public function isLink() {
        return $this->type == self::TYPE_LINK;
    }

    /** URL большого изображения
     * @return null|string
     */
    public function getImageBig() {
        /** @var $media Media[] */
        $media = array_filter($this->medias, function(Media $media) { return in_array(self::TAG_IMAGE_BIG, $media->tags); });
        return $media ? reset($media)->getOriginalImage() : null;
    }

    /** URL большого изображения
     * @return null|string
     */
    public function getImageSmall() {
        /** @var $media Media[] */
        $media = array_filter($this->medias, function(Media $media) { return in_array(self::TAG_IMAGE_SMALL, $media->tags); });
        return $media ? reset($media)->getOriginalImage() : null;
    }

}