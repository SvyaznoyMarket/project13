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
   * @param $callback
   * @param array $filters
   * @param array $sort
   * @param null $offset
   * @param null $limit
   */
  public function getListingAsync($callback, $filters = array(), $sort = array(), $offset = null, $limit = null)
  {
    CoreClient::getInstance()->addQuery("listing.list", array(
      "filter" => array(
        'filters' => $filters,
        'sort' => $sort,
        'offset' => $offset,
        'limit' => $limit,
      ),
      "region_id" => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), array(), $callback);
  }

  /**
   *
   * @param array $filterList
   * <pre>
   * [
   *    {
   *        limit: 10, // optional
   *        offset: 20, // optional
   *        filters: [   // optional
   *            // property name, filter type range, min, max, is filter exclude
   *            [id, 2, 199, 1000, true],
   *            // property name, filter type values, list of values, is filter exclude
   *            [category, 1, [1,2,3,4], false],
   *            [brand, 1, [1]],
   *            [price, 2, 1000, 10000],
   *            [tag, 1, [44]],
   *            // some additional property
   *            // property id, filter type range, min, max, is filter exclude
   *            [1, 2, 10, 1999],
   *            // property name, filter type values, list of values, is filter exclude
   *            [2, 1, ["foo"]],
   *        ],
   *        sort: {id: "desc"},  // optional
   *    }
   * ]
   * </pre>
   * @return array
   * <pre>
   * [
   *    {
   *        list: [1,2,3,4,5],
   *        count: 134,
   *    },
   *    ... // another answers
   * ];
   * </pre>
   */
  public function getListingMultiple($filterList = array())
  {
    return CoreClient::getInstance()->query("listing.multilist", array(), array(
      "filter_list" => $filterList,
      "region_id" => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
  }
}
