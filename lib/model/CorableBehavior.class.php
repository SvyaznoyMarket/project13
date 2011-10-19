<?php

class Doctrine_Template_Corable extends Doctrine_Template
{

  public function setTableDefinition()
  {
    $this->hasColumn('core_id', 'integer', 20, array(
      'type' => 'integer',
      'notnull' => false,
      'comment' => 'ид записи в Core',
      'length' => 20,
    ));

    $this->addListener(new Doctrine_Template_Listener_Corable($this->_options));
  }

  public function getListByCoreIdsTableProxy(array $coreIds, array $params = array())
  {
    $table = $this->getInvoker()->getTable();
    $q = $table->createBaseQuery($params);

    $q->whereIn($q->getRootAlias().'.core_id', $coreIds);

    $ids = $table->getIdsByQuery($q, $params);

    return $table->createListByIds($ids, $params);
  }
}
