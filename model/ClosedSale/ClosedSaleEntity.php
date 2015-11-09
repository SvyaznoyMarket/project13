<?php

namespace Model\ClosedSale;


use Model\Media;
use Model\Product\Entity as Product;

class ClosedSaleEntity
{

    const MEDIA_SMALL = 'closed_sale_315x231';
    const MEDIA_MEDIUM = 'closed_sale_483x357';
    const MEDIA_BIG = 'closed_sale_651x483';
    const MEDIA_FULL = 'closed_sale_987x725';

    /** @var string  */
    public $uid;
    /** @var string  */
    public $name;
    /** @var int  */
    public $discount;
    /** @var \DateTime  */
    public $startsAt;
    /** @var \DateTime  */
    public $endsAt;
    /** @var int  */
    public $priority;
    /** @var Media[]  */
    public $medias = [];
    /** @var Product[] */
    public $products = [];

    public function __construct(array $arr)
    {
        if (array_key_exists('uid', $arr)) {
            $this->uid = $arr['uid'];
        }

        if (array_key_exists('name', $arr)) {
            $this->name = $arr['name'];
        }

        if (array_key_exists('discount', $arr)) {
            $this->discount = $arr['discount'];
        }

        if (array_key_exists('starts_at', $arr)) {
            $this->startsAt = new \DateTime($arr['starts_at']);
        }

        if (array_key_exists('expires_at', $arr)) {
            $this->endsAt = new \DateTime($arr['expires_at']);
        }

        if (array_key_exists('priority', $arr)) {
            $this->priority = $arr['priority'];
        }

        if (array_key_exists('medias', $arr) && is_array($arr['medias'])) {
            $this->medias = array_map(
                function ($media) {
                    return new Media($media);
                },
                $arr['medias']
            );
        }

        if (array_key_exists('products', $arr) && is_array($arr['products'])) {
            $this->products = array_map(
                function ($data) {
                    return new Product($data);
                },
                $arr['products']
            );
        }
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return array_key_exists(0, $this->medias) ? $this->medias[0] : new Media();
    }

    /**
     * Возвращает активность акции основанную на времени начала и времени окончания относительно текущего времени
     * @return bool
     */
    public function isActive()
    {
        $time = new \DateTime();
        return $this->startsAt < $time && $this->endsAt > $time;
    }

    /** Возвращает разницу во времени
     * @return \DateInterval|null
     */
    public function getDateDiff() {
        if ($this->endsAt instanceof \DateTime) {
            return date_diff($this->endsAt, new \DateTime(), true);
        }
        return null;
    }

    /** Возвращает '2 дня 2:33:59'
     * @return string
     */
    public function getDateDiffString() {
        if ($diff = $this->getDateDiff()) {
            if ($diff->days !== 0) {
                return \App::helper()->numberChoiceWithCount($diff->days, ['день', 'дня', 'дней']). ' '. $diff->format('%H:%I:%S');
            } else {
                return $diff->format('%H:%I:%S');
            }
        }
        return '';
    }
}
