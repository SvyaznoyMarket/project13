<?php

namespace Model\Banner;

use Model\Media;

class BannerEntity {

    const TYPE_PAGE = 'static-page';
    const TYPE_SLICE = 'slice';
    const TYPE_CATEGORY = 'category';

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

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('uid', $data)) $this->uid = $data['uid'];
        if (array_key_exists('type', $data)) $this->type = $data['type'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
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