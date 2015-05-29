<?php

namespace Model\Product;

use Model\Media;
use Util\Date;

/** Шильдик из SCMS
 * Class Label
 * @package Product\Label
 */
class Label {

    const MEDIA_TAG_IMAGE_TOP = 'site-image-hover';
    const MEDIA_TAG_RIGHT_SIDE = 'site-right-side';

    /** @var string */
    public $uid;
    /** @var string|null */
    public $name;
    /** @var bool */
    public $affectPrice = false;
    /** @var \DateTime|null */
    public $expires;
    /** @var Media[] */
    public $medias = [];

    public function __construct($data) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['affect_price'])) $this->affectPrice = $data['affect_price'];
        if (isset($data['expires_at']) && Date::isDateTimeString($data['expires_at'])) {
            $this->expires = new \DateTime($data['expires_at']);
        }
        if (isset($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media)
                $this->medias[] = new Media($media);
        }
    }

    /** Возвращает URL картинки с заданным тэгом
     * @param $tag
     * @return null|string
     */
    public function getImageUrlWithTag($tag) {
        foreach ($this->medias as $media) {
            if (in_array($tag, $media->tags) && $media->getOriginalImage()) return $media->getOriginalImage();
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isExpired() {
        return $this->expires instanceof \DateTime ? new \DateTime() > $this->expires : false;
    }

    /** Возвращает разницу во времени
     * @return \DateInterval|null
     */
    public function getDateDiff() {
        if ($this->expires instanceof \DateTime) return date_diff($this->expires, new \DateTime(), true);
        return null;
    }

    /** Возвращает '2 дня 2:33:59'
     * @return string
     */
    public function getDateDiffString() {
        if ($diff = $this->getDateDiff()) return \App::helper()->numberChoiceWithCount($diff->days, ['день', 'дня', 'дней'])
            . ' '. $diff->format('%h:%I:%S');
        return '';
    }

    /**
     * @return null|string
     */
    public function getImageUrl() {
        return $this->getImageUrlWithTag(self::MEDIA_TAG_IMAGE_TOP);
    }

    public function getName() {
        return $this->name;
    }

}