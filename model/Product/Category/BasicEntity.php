<?php

namespace Model\Product\Category;

use Session\AbTest\ABHelperTrait;

abstract class BasicEntity {
    use ABHelperTrait;

    const VIEW_COMPACT = 1;
    const VIEW_EXPANDED = 2;
    const VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION = 3;
    const VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION = 4;
    const VIEW_LIGHT_WITHOUT_DESCRIPTION = 5;

    const UI_MEBEL                                       = 'f7a2f781-c776-4342-81e8-ab2ebe24c51a';
    const UI_SDELAY_SAM                                  = '0e80c81b-31c9-4519-bd10-e6a556fe000c';
    const UI_DETSKIE_TOVARY                              = 'feccd951-d555-42c2-b417-a161a78faf03';
    const UI_DETSKIE_TOVARY_AKSESSUARY_DLYA_AVTOKRESEL   = 'f30feba1-915e-40e5-9344-35b535085a76';
    const UI_TOVARY_DLYA_DOMA                            = 'b8569e65-e31e-47a1-af20-5b06aff9f189';
    const UI_TOVARY_DLYA_DOMA_AKSESSUARY_DLYA_VANNOI     = 'ed1ac096-66b8-4b55-9941-34ade3dc6725';
    const UI_ELECTRONIKA                                 = 'd91b814f-0470-4fd5-a2d0-a0449e63ab6f';
    const UI_PODARKI_I_HOBBY                             = 'c9c2dc8d-1ee5-4355-a0c1-898f219eb892';
    const UI_BYTOVAYA_TEHNIKA                            = '616e6afd-fd4d-4ff4-9fe1-8f78236d9be6';
    const UI_BYTOVAYA_TEHNIKA_AKSESSUARY                 = 'a72e6335-d62c-4a46-85a6-306cd1c8af14';
    const UI_UKRASHENIYA_I_CHASY                         = '022fa1e3-c51f-4a48-87fc-de2c917176d6';
    const UI_PARFUMERIA_I_COSMETIKA                      = '19b9f12c-d489-4540-9a17-23dba0641166';
    const UI_SPORT_I_OTDYH                               = '846eccd2-e9f0-4ce4-b7a2-bb28a835fd7a';
    const UI_SPORT_I_OTDYH_VELOSIPEDY_AKSESSUARY         = '1f087575-d6c2-45f4-8c1b-dadabca45141';
    const UI_ZOOTOVARY                                   = 'b933de12-5037-46db-95a4-370779bb4ee2';
    const UI_ZOOTOVARY_AKSESSUARY_DLYA_AKVARIUMOV        = 'eba838c7-77f0-4e75-a631-b8280caddfc2';
    const UI_TCHIBO                                      = 'caf18e17-550a-4d3e-8285-b1c9cc99b5f4';
    const UI_KRASOTA_I_ZDOROVIE                          = '5f3aa3be-1ac2-4dff-a473-c603e6e51e41';
    const UI_ELECTRONIKA_AKSESSUARY                      = '5e78849d-01e8-4509-8bfe-85f8e148b37d';
    const UI_IGRY_I_KONSOLI                              = 'ed807fca-962b-4b75-9813-d5efbb8ef586';
    const UI_AVTO                                        = 'f0d53c46-d4fc-413f-b5b3-a2b57b93a717';
    const UI_ODEZHDA                                     = 'df56d956-3b07-4fca-ad47-d116a0f5104e';
    const UI_ODEZHDA_AKSESSUARY                          = '6270ed26-3582-4749-8e0d-2e8373f600b0';
    const UI_SAD_I_OGOROD                                = 'e86ccf17-e161-4d5e-8158-2a8ee458b8e7';

    // TCHIBO
    const UI_TCHIBO_COLLECTIONS         = 'de9f59d7-bade-4a77-9190-060cf5b834e3';
    const UI_TCHIBO_SALE                = '7779fe2f-13c1-40dc-9de7-57f983ccb6dc';

    /** @var int|null */
    public $id;
    /** @var string|null */
    public $ui;
    /** @var int|null */
    protected $parentId;
    /** @var string|null */
    public $name;
    /** @var string|null */
    protected $link;
    /** @var string|null */
    protected $token;
    /** @var int|null */
    protected $level;

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
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = (string)$ui;
    }

    /**
     * @return string|null
     */
    public function getUi() {
        return $this->ui;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = rtrim((string)$link, '/');
    }

    /**
     * @param array|mixed $queryParams
     * @return string
     */
    public function getLink($queryParams = []) {
        if ($queryParams) {
            return $this->link . (strpos($this->link, '?') === false ? '?' : '&') . http_build_query($queryParams);
        } else {
            return $this->link;
        }
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
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/catalog\//' , '', $this->link), '/');
    }
}