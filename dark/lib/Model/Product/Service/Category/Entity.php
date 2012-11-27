<?php

namespace Model\Product\Service\Category;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $parentId;
    /** @var string */
    private $name;
    /** @var string */
    private $link;
    /** @var string */
    private $token;
    /** @var string */
    private $image;
    /** @var string */
    private $description;
    /** @var int */
    private $level;
    /** @var Entity[] */
    private $child = array();
    /** @var Entity|null */
    private $parent;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('level', $data)) $this->setLevel($data['level']);
        if (array_key_exists('children', $data) && is_array($data['children'])) foreach ($data['children'] as $childData) {
            $this->addChild(new Entity($childData));
        }

        if (false !== strpos($this->token, 'mebel')) {
            $this->setDescription('Соберем любой шкаф, и&nbsp;при этом не&nbsp;останется ни&nbsp;одной &laquo;лишней&raquo; детали. Занесем диван хоть на&nbsp;35-й&nbsp;этаж (а&nbsp;можем и на 36-й). Повесим все необходимые шкафчики на&nbsp;кухне в&nbsp;правильной последовательности (и&nbsp;обязательно уберём за&nbsp;собой весь мусор).');
        } else if (false !== strpos($this->token, 'bitovaya-tehnika')) {
            $this->setDescription('Подключим стиральную и&nbsp;посудомоечную машину, установим кондиционер, даже если потребуются альпработы. Повесим телевизор на&nbsp;стену, подключим всю кухонную технику, установим водонагреватель любого типа или&nbsp;комплект спутникового телевидения.');
        } else if (false !== strpos($this->token, 'elektronika')) {
            $this->setDescription('Поможем настроить &laquo;умный&raquo; ТВ и&nbsp;подключить его к&nbsp;Интернету. Настроим Wi-Fi-роутер или&nbsp;проложим сетевой кабель. Подключим и&nbsp;настроим любую компьютерную технику или&nbsp;игровую приставку. Установим программы на&nbsp;компьютер или смартфон и&nbsp;научим их&nbsp;эффективно использовать.');
        } else if (false !== strpos($this->token, 'sport')) {
            $this->setDescription('Соберем новый велосипед и&nbsp;научим вас разбирать его в&nbsp;случае необходимости. Прокачаем вашего &laquo;двухколесного друга&raquo;, установив на&nbsp;него дополнительные аксессуары. А&nbsp;еще мы устанавливаем крепления на&nbsp;лыжи (беговые или&nbsp;горные) и&nbsp;даже на&nbsp;сноуборд.');
        }
    }

    /**
     * @param Entity[] $child
     */
    public function setChild(array $children) {
        $this->child = array();
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param Entity $child
     */
    public function addChild(Entity $child) {
        $this->child[] = $child;
    }

    /**
     * @return array|Entity[]
     */
    public function getChild() {
        return $this->child;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 1) {
        return $this->image ? \App::config()->service['url'][$size] . $this->image : null;
    }

    /**
     * @param int $level
     */
    public function setLevel($level) {
        $this->level = (int)$level;
    }

    /**
     * @return int
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param \Model\Product\Service\Category\Entity|null $parent
     */
    public function setParent(Entity $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return \Model\Product\Service\Category\Entity|null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId) {
        $this->parentId = (int)$parentId;
    }

    /**
     * @return int
     */
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
}