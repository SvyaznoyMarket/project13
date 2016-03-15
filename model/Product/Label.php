<?php

namespace Model\Product;

use Model\Media;
use Util\Date;

/** Шильдик из SCMS
 * Class Label
 * @package Product\Label
 */
class Label {

    const MEDIA_TAG_IMAGE_TOP = '66x23';
    const MEDIA_TAG_RIGHT_SIDE = 'label-preview';

    /** @var string|int */
    public $id;
    /** @var string */
    public $uid;
    /** @var string|null */
    public $name;
    /** @var bool */
    public $affectPrice = false;
    /** @var \DateTime|null */
    public $expires;
    /** @var string|null */
    public $url;
    /** @var Media[] */
    public $medias = [];
    /** @var bool */
    public $showExpirationDate;

    public function __construct($data) {
        if (isset($data['core_id'])) $this->id = $data['core_id'];
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['affects_price'])) $this->affectPrice = $data['affects_price'];
        if (
            isset($data['expires_at'])
            && is_string($data['expires_at'])
            && !in_array($this->uid, ['0d7ebd2d-39fc-4973-9f4d-a81869862180', '3f781954-122c-4555-940f-66d731242d33'])
        ) {
            $this->expires = new \DateTime($data['expires_at']);
        }
        if (isset($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media)
                $this->medias[] = new Media($media);
        }
        if (isset($data['show_expiration_date'])) $this->showExpirationDate = (bool)$data['show_expiration_date'];
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
        if ($diff = $this->getDateDiff()) {
            if ($diff->days != 0) {
                return \App::helper()->numberChoiceWithCount($diff->days, ['день', 'дня', 'дней']). ' '. $diff->format('%H:%I:%S');
            } else {
                return $diff->format('%H:%I:%S');
            }
        }
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