<?php

namespace Model\Supplier;


class File {

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $mimeType;
    /** @var string */
    public $size;
    /** @var string */
    private $url;
    /** @var \DateTime  */
    public $added;
    /** @var \DateTime */
    public $updated;

    public function __construct($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['origin_name'])) $this->name = $data['origin_name'];
        if (isset($data['mime_type'])) $this->mimeType = $data['mime_type'];
        if (isset($data['file_size'])) $this->size = $data['file_size'];
        if (isset($data['url'])) $this->url = $data['url'];
        if (isset($data['added'])) $this->added = new \DateTime($data['added']);
        if (isset($data['updated'])) $this->updated = new \DateTime($data['updated']);
    }

    public function getUrl(){
        return $this->url;
    }

    /** Excel-файл?
     * @return bool
     */
    public function isExcelFile() {
        // Список типов взят из
        /** @link  http://stackoverflow.com/questions/974079/setting-mime-type-for-excel-document */
        return in_array($this->mimeType, [
            'application/vnd.ms-excel',
            'application/msexcel',
            'application/x-msexcel',
            'application/x-ms-excel',
            'application/x-excel',
            'application/x-dos_ms_excel',
            'application/xls',
            'application/x-xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

}