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

namespace steky\nette\gallery;

/**
 * Define how to communicate with database.
 */
interface IDataProvider {
	
	/**
	 * Returs associative array of namespaces.
	 * 
	 * @return array
	 */
	public function getNamespaces();
	
	/**
	 * Creates new group of items. Returns ID for new created group.
	 * 
	 * @param array $group_data Array of group data
	 * @return int|string GroupID
	 */
	public function createGroup(array $group_data);
	
	/**
	 * Updates given group by new data or insert new files.
	 * 
	 * @param int|string $group_id GroupID
	 * @param array $update_data
	 */
	public function updateGroup($group_id, array $update_data = array());
	
	/**
	 * Change activity for given group.
	 * 
	 * @param int|string $group_id GroupID
	 */
	public function toggleActiveGroup($group_id);
	
	/**
	 * Delete given group.
	 * 
	 * @param int|string $group_id GroupID
	 */
	public function deleteGroup($group_id);
	
	/**
	 * Returns count of groups in given namespace.
	 * 
	 * @param int $namespace_id
	 * @param bool $admin
	 * @return int
	 */
	public function getGroupCount($namespace_id, $admin = false);
	
	/**
	 * Returns data for all groups in given namespace. This helps with paging.
	 * 
	 * @param int $namespace_id
	 * @param bool $admin
	 * @param int $page
	 * @param int $itemPerPage
	 * @return array
	 */
	public function getAllGroups($namespace_id, $admin = false, $page = 1, $itemPerPage = 25);
	
	/**
	 * Returns data for given group.
	 * 
	 * @param int|string $group_id GroupID
	 * @return array
	 */
	public function getGroupById($group_id);
	
	/**
	 * Creates new item. Returns ID for new created item.
	 * 
	 * @param array $item_data Array of item data
	 * @param int|string $group_id Identify in which group item is
	 * @return int|string ItemID
	 */
	public function createItem(array $item_data, $group_id);
	
	/**
	 * Updates given item by new data.
	 * 
	 * @param int|string $item_id ItemID
	 * @param array $update_data
	 */
	public function updateItem($item_id, array $update_data = array());
	
	/**
	 * Change activity for given item.
	 * 
	 * @param int|string $item_id ItemID
	 */
	public function toggleActiveItem($item_id);
	
	/**
	 * Delete given item.
	 * 
	 * @param int|string $item_id ItemID
	 */
	public function deleteItem($item_id);
	
	/**
	 * Returns data for all items in given group.
	 * 
	 * @param int|string $group_id GroupID
	 * @param bool $admin
	 * @return array
	 */
	public function getItemsByGroup($group_id, $admin = false);
	
	/**
	 * Returns data for given item.
	 * 
	 * @param int|string $item_id ItemID
	 * @return array
	 */
	public function getItemById($item_id);
	
	/**
	 * Returns ItemID for item which is previous given item.
	 * 
	 * @param int|string $item_id ItemID
	 * @return int|string ItemID for item on the left side 
	 */
	public function getLeftItemBy($item_id);
	
	/**
	 * Returns ItemID for item which is next to given item.
	 * 
	 * @param int|string $item_id ItemID
	 * @return int|string ItemID for item on the right side 
	 */
	public function getRightItemBy($item_id);

	/**
	 * Change ordering for given items -> switch them.
	 * 
	 * @param int|string $item_id_1
	 * @param int|string $item_id_2 
	 */
	public function swapItems($item_id_1, $item_id_2);
	
}
