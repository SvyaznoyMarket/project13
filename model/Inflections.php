<?php
namespace Model;

class Inflections {
    /**
     * Именительный падеж (например: Бытовая техника)
     * @var string
     */
    public $nominativus = '';

    /**
     * Родительный падеж (например: Бытовой техники)
     * @var string
     */
    public $genitivus = '';

    /**
     * Дательный падеж (например: Бытовой технике)
     * @var string
     */
    public $dativus = '';

    /**
     * Винительный падеж (например: Бытовую технику)
     * @var string
     */
    public $accusativus = '';

    /**
     * Творительный падеж (например: Бытовой техникой)
     * @var string
     */
    public $ablativus = '';

    /**
     * Предложный падеж (например: Бытовой технике)
     * @var string
     */
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