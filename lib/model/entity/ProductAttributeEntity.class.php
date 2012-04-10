<?php

/**
 * Атрибут товара
 * @see ProductEntity
 */
class ProductAttributeEntity
{
  private $id;
  private $name;
  private $value;
  /** @var ProductPropertyOptionEntity[] */
  private $optionList = array();
  private $unit;
  private $hint;
  private $position;
  private $groupId;
  private $groupPosition;
  private $isViewList;
  private $isViewCard;
  private $isMultiple;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data)) $this->setId($data['id']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
    if (array_key_exists('value', $data)) $this->setValue($data['value']);
    if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
    if (array_key_exists('hint', $data)) $this->setHint($data['hint']);
    if (array_key_exists('position', $data)) $this->setPosition($data['position']);
    if (array_key_exists('group_id', $data)) $this->setGroupId($data['group_id']);
    if (array_key_exists('group_position', $data)) $this->setGroupPosition($data['group_position']);
    if (array_key_exists('is_view_list', $data)) $this->setIsViewList($data['is_view_list']);
    if (array_key_exists('is_view_card', $data)) $this->setIsViewCard($data['is_view_card']);
    if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
  }

  /**
   * @param int $group_id
   */
  public function setGroupId($group_id)
  {
    $this->groupId = (int)$group_id;
  }

  /**
   * @return int
   */
  public function getGroupId()
  {
    return $this->groupId;
  }

  /**
   * @param int $group_position
   */
  public function setGroupPosition($group_position)
  {
    $this->groupPosition = (int)$group_position;
  }

  /**
   * @return int
   */
  public function getGroupPosition()
  {
    return $this->groupPosition;
  }

  /**
   * @param $hint
   */
  public function setHint($hint)
  {
    $this->hint = $hint;
  }

  /**
   * @return mixed
   */
  public function getHint()
  {
    return $this->hint;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = (int)$id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param boolean $is_multiple
   */
  public function setIsMultiple($is_multiple)
  {
    $this->isMultiple = (boolean)$is_multiple;
  }

  /**
   * @return boolean
   */
  public function getIsMultiple()
  {
    return $this->isMultiple;
  }

  /**
   * @param boolean $is_view_card
   */
  public function setIsViewCard($is_view_card)
  {
    $this->isViewCard = (boolean)$is_view_card;
  }

  /**
   * @return boolean
   */
  public function getIsViewCard()
  {
    return $this->isViewCard;
  }

  /**
   * @param boolean $is_view_list
   */
  public function setIsViewList($is_view_list)
  {
    $this->isViewList = (boolean)$is_view_list;
  }

  /**
   * @return boolean
   */
  public function getIsViewList()
  {
    return $this->isViewList;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = (string)$name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param ProductPropertyOptionEntity[] $optionList
   */
  public function setOptionList($optionList)
  {
    $this->optionList = array();
    foreach ($optionList as $option)
      $this->addOption($option);
  }

  /**
   * @param ProductPropertyOptionEntity $option
   */
  public function addOption(ProductPropertyOptionEntity $option)
  {
    $this->optionList[] = $option;
  }

  /**
   * @return ProductPropertyOptionEntity[]
   */
  public function getOptionList()
  {
    return $this->optionList;
  }

  /**
   * @param int $position
   */
  public function setPosition($position)
  {
    $this->position = (int)$position;
  }

  /**
   * @return int
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = (string)$unit;
  }

  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getStringValue()
  {
    if (!empty($this->optionList)) {
      $value = array();
      foreach ($this->optionList as $option)
        $value[] = $option->getHumanizedName();
      return join(', ', $value);
    }
    else
    {
      if (in_array($this->value, array('false', false), true)) {
        return 'нет';
      }
      if (in_array($this->value, array('true', true), true)) {
        return 'да';
      }
      return $this->value;
    }
  }
}
