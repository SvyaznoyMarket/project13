<?php
/**
 * Created by PhpStorm.
 * User: rmn
 * Date: 24.11.14
 * Time: 14:30
 */

namespace Model\Menu;


class BasicMenuEntity {

    public $id;
    public $name;
    public $link;
    public $image;
    public $char;
    public $children;

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = (int)$data['id'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
        if (isset($data['link'])) $this->link = preg_replace('/\/$/', '', (string)$data['link']);
        if (isset($data['image'])) $this->image = (string)$data['image'];

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $item) $this->children[$item['id']] = new BasicMenuEntity($item);
        }

    }

}