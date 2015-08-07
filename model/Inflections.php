<?php
namespace Model;

class Inflections {
    /** @var string */
    public $nominativus = '';
    /** @var string */
    public $genitivus = '';
    /** @var string */
    public $dativus = '';
    /** @var string */
    public $accusativus = '';
    /** @var string */
    public $ablativus = '';
    /** @var string */
    public $locativus = '';

    public function __construct($data = []) {
        if (isset($data['nominativus'])) $this->nominativus = (string)$data['nominativus'];
        if (isset($data['genitivus'])) $this->genitivus = (string)$data['genitivus'];
        if (isset($data['dativus'])) $this->dativus = (string)$data['dativus'];
        if (isset($data['accusativus'])) $this->accusativus = (string)$data['accusativus'];
        if (isset($data['ablativus'])) $this->ablativus = (string)$data['ablativus'];
        if (isset($data['locativus'])) $this->locativus = (string)$data['locativus'];
    }
}