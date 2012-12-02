<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2012.01.30
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace stekycz\gallery\DataProvider;
use stekycz\gallery\IDataProvider;
use Nette\Object;
use Nette\Caching\Cache;

/**
 * Decorator for all data provider which allows caching of results.
 */
class CachableProvider extends Object implements IDataProvider {

	/**
	 * @var \Nette\Caching\Cache
	 */
	private $cache;

	/**
	 * @var \stekycz\gallery\IDataProvider
	 */
	private $provider;

	/**
	 * @param \stekycz\gallery\IDataProvider $provider
	 * @param \Nette\Caching\Cache $cache
	 */
	public function __construct(IDataProvider $provider, Cache $cache) {
		$this->provider = $provider;
		$this->cache = $cache->derive('gallery');
	}

	/**
	 * Saves given data under given key for this class.
	 *
	 * @param string $key
	 * @param mixed $data
	 */
	private function saveToCache($key, $data) {
		$params = array(
			Cache::TAGS => array('gallery', ),
			Cache::EXPIRATION => 30,
			Cache::SLIDING => false,
		);
		$this->cache->save($key, $data, $params);
	}

	/**
	 * Returs associative array of namespaces.
	 *
	 * @return array
	 */
	public function getNamespaces() {
		$key = 'namespaces';
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getNamespaces();
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Creates new group of items. Returns ID for new created group.
	 *
	 * @param array $group_data Array of group data
	 * @return int|string GroupID
	 */
	public function createGroup(array $group_data) {
		return $this->provider->createGroup($group_data);
	}

	/**
	 * Updates given group by new data or insert new files.
	 *
	 * @param int|string $group_id GroupID
	 * @param array $update_data
	 */
	public function updateGroup($group_id, array $update_data = array()) {
		$this->provider->updateGroup($group_id, $update_data);
	}

	/**
	 * Change activity for given group.
	 *
	 * @param int|string $group_id GroupID
	 */
	public function toggleActiveGroup($group_id) {
		$this->provider->toggleActiveGroup($group_id);
	}

	/**
	 * Delete given group.
	 *
	 * @param int|string $group_id GroupID
	 */
	public function deleteGroup($group_id) {
		$this->deleteGroup($group_id);
	}

	/**
	 * Returns count of groups in given namespace.
	 *
	 * @param int $namespace_id
	 * @param bool $admin
	 * @return int
	 */
	public function countGroups($namespace_id, $admin = false) {
		$key = 'groups-count-' . $namespace_id . $admin;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->countGroups($namespace_id, $admin);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Returns data for all groups in given namespace. This helps with paging.
	 *
	 * @param int $namespace_id
	 * @param bool $admin
	 * @param int $page
	 * @param int $itemPerPage
	 * @return array
	 */
	public function getAllGroups($namespace_id, $admin = false, $page = 1, $itemPerPage = 25) {
		$key = 'groups-' . $namespace_id . $admin . $page . $itemPerPage;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getAllGroups($namespace_id, $admin, $page, $itemPerPage);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Returns data for given group.
	 *
	 * @param int|string $group_id GroupID
	 * @return array
	 */
	public function getGroupById($group_id) {
		$key = 'group-' . $group_id;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getGroupById($group_id);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Creates new item. Returns ID for new created item.
	 *
	 * @param array $item_data Array of item data
	 * @param int|string $group_id Identify in which group item is
	 * @return int|string ItemID
	 */
	public function createItem(array $item_data, $group_id) {
		return $this->provider->createItem($item_data, $group_id);
	}

	/**
	 * Updates given item by new data.
	 *
	 * @param int|string $item_id ItemID
	 * @param array $update_data
	 */
	public function updateItem($item_id, array $update_data = array()) {
		$this->provider->updateItem($item_id, $update_data);
	}

	/**
	 * Change activity for given item.
	 *
	 * @param int|string $item_id ItemID
	 */
	public function toggleActiveItem($item_id) {
		$this->provider->toggleActiveItem($item_id);
	}

	/**
	 * Delete given item.
	 *
	 * @param int|string $item_id ItemID
	 */
	public function deleteItem($item_id) {
		$this->provider->deleteItem($item_id);
	}

	/**
	 * Returns data for all items in given group.
	 *
	 * @param int|string $group_id GroupID
	 * @param bool $admin
	 * @return array
	 */
	public function getItemsByGroup($group_id, $admin = false) {
		$key = 'item-by-group-' . $group_id . $admin;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getItemsByGroup($group_id, $admin);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Returns data for given item.
	 *
	 * @param int|string $item_id ItemID
	 * @return array
	 */
	public function getItemById($item_id) {
		$key = 'item-' . $item_id;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getItemById($item_id);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Returns ItemID for item which is previous given item.
	 *
	 * @param int|string $item_id ItemID
	 * @return int|string ItemID for item on the left side
	 */
	public function getLeftItemBy($item_id) {
		$key = 'left-by-' . $item_id;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getLeftItemBy($item_id);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Returns ItemID for item which is next to given item.
	 *
	 * @param int|string $item_id ItemID
	 * @return int|string ItemID for item on the right side
	 */
	public function getRightItemBy($item_id) {
		$key = 'right-by-' . $item_id;
		if (!($result = $this->cache->load($key))) {
			$result = $this->provider->getRightItemBy($item_id);
			$this->saveToCache($key, $result);
		}
		return $result;
	}

	/**
	 * Change ordering for given items -> switch them.
	 *
	 * @param int|string $item_id_1
	 * @param int|string $item_id_2
	 */
	public function swapItems($item_id_1, $item_id_2) {
		$this->provider->swapItems($item_id_1, $item_id_2);
	}

}
