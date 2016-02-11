<?php

namespace Model\Menu;

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
        if (isset($data['ui'])) $this->ui = (string)$data['ui'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
        if (isset($data['link'])) $this->link = preg_replace('/\/$/', '', (string)$data['link']);

        // Пропускаем url через Source для подмены URL в ветке lite
        if (isset($data['media_image'])) $this->image = (new \Model\Media\Source(['url' => $data['media_image']]))->url;

        // Пропускаем url через Source для подмены URL в ветке lite
        if (isset($data['logo_path']) && isset($data['use_logo']) && $data['use_logo']) $this->logo = (new \Model\Media\Source(['url' => $data['logo_path']]))->url;

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $item) $this->children[$item['id']] = new BasicMenuEntity($item);
        }
    }
}