<?php

namespace Model\ClosedSale;


use Model\Media;

class ClosedSaleEntity
{

    const MEDIA_SMALL = 'closed_sale_315x231';
    const MEDIA_MEDIUM = 'closed_sale_483x357';
    const MEDIA_BIG = 'closed_sale_651x483';
    const MEDIA_FULL = 'closed_sale_987x725';

    // Время действия акции
    const ACTION_DAYS = 2;

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

        if (array_key_exists('ends_at', $arr)) {
            $this->endsAt = new \DateTime($arr['ends_at']);
        } else {
            $start = clone $this->startsAt;
            $this->endsAt = $start
                ? $start->add(new \DateInterval(sprintf('P%sD', self::ACTION_DAYS)))
                : new \DateTime(sprintf('%s days', self::ACTION_DAYS));
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
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return array_key_exists(0, $this->medias) ? $this->medias[0] : new Media();
    }
}
