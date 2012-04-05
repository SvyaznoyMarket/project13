<?php

class ListingRepository
{
  /**
   * @param array $filters
   * @param array $sort
   * @param null $offset
   * @param null $limit
   * @return array Response example
   * <pre>
   * array(
   *  "list" => array(1,2,3,4,5),
   *  "count" => 134,
   * );
   * </pre>
   */
  public function getListing($filters = array(), $sort = array(), $offset = null, $limit = null)
  {
    return CoreClient::getInstance()->query("listing.list", array(
      "filter" => array(
        'filters' => $filters,
        'sort' => $sort,
        'offset' => $offset,
        'limit' => $limit,
      ),
      "region_id" => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
  }

  /**
   * @param callback $callback First parameter:
   * <pre>
   * array(
   *  "list" => array(1,2,3,4,5),
   *  "count" => 134,
   * );
   * </pre>
   * @param array $filters
   * @param array $sort
   * @param null $offset
   * @param null $limit
   */
  public function getListingAsync($callback, $filters = array(), $sort = array(), $offset = null, $limit = null)
  {
    CoreClient::getInstance()->addQuery("listing.list", $query = array(
      "filter" => array(
        'filters' => $filters,
        'sort' => $sort,
        'offset' => $offset,
        'limit' => $limit,
      ),
      "region_id" => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), array(), $callback);
  }
}
