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

    $q->select('id')
      ->where('core_id = ?', $coreId)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    $id = $q->fetchOne();

    return $id ? $id : null;
  }

  public function getCoreIdByIdTableProxy($id, array $params = array())
  {
    if (!$id)
    {
      return false;
    }

    $table = $this->getInvoker()->getTable();
    $q = $table->createQuery();

    $q->select('core_id')
      ->where('id = ?', $id)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    $coreId = $q->fetchOne();

    return $coreId ? $coreId : null;
  }

  public function getCoreIdsByIdsTableProxy($ids, array $params = array())
  {
    if (!is_array($ids) || !count($ids))
    {
      return false;
    }

    $table = $this->getInvoker()->getTable();
    $q = $table->createQuery();

    $q->select('core_id')
      ->whereIn('id', $ids)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    $coreIds = $q->execute();

    return is_array($coreIds) ? $coreIds : array($coreIds);
  }
}
