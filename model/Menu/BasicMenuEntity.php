<?php
/**
 * Created by PhpStorm.
 * User: rmn
 * Date: 24.11.14
 * Time: 14:30
 */

namespace Model\Menu;

use Model\Media;
use Model\Medias;

class BasicMenuEntity {
    public $id;
    public $ui;
    public $name;
    public $link;
    public $image;
    public $logo;
    public $char;
    public $children = [];

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = (int)$data['id'];
        if (isset($data['uid'])) $this->ui = (string)$data['uid'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
        if (isset($data['link'])) $this->link = preg_replace('/\/$/', '', (string)$data['link']);
        if (isset($data['logo_path']) && isset($data['use_logo']) && $data['use_logo']) $this->logo = (string)$data['logo_path']; // TODO FCMS-941

        if (isset($data['medias']) && is_array($data['medias'])) {
            $medias = new Medias($data['medias']);
            $this->image = $medias->getMediaSource('category_163x163')->url;

            // TODO удалить после релиза FCMS-932
            if (!$this->image) {
                $this->image = $medias->getMediaSource('category_163x163', '')->url;
            }
        }

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $item) $this->children[$item['id']] = new BasicMenuEntity($item);
        }
    }
}