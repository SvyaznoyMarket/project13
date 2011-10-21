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
    // TODO: вынести в myDoctrineTable
    if (isset($params['order']) && ('_index' == $params['order']))
    {
      $q->orderBy('FIELD(core_id, '.implode(',', $coreIds).')');
    }

    $ids = $table->getIdsByQuery($q, $params);

    return $table->createListByIds($ids, $params);
  }

  public function getByCoreIdTableProxy($coreId, array $params = array())
  {
    if (!$coreId)
    {
      return false;
    }

    $table = $this->getInvoker()->getTable();
    $q = $table->createQuery();

    $q->where('core_id = ?', $coreId);

    return $q->fetchOne();
  }

  public function getIdByCoreIdTableProxy($coreId, array $params = array())
  {
    if (!$coreId)
    {
      return false;
    }

    $table = $this->getInvoker()->getTable();
    $q = $table->createQuery();

    $q
      ->where('core_id = ?', $coreId)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    return $q->fetchOne();
  }
}
