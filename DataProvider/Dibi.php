<?php

namespace steky\nette\gallery\DataProvider;
use \Nette,
	\steky\nette\gallery,
	\DibiConnection;

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2012.01.30
 */
class Dibi extends Nette\Object implements gallery\IDataProvider {

	/**
	 * @var DibiConnection
	 */
	private $connection;

	/**
	 * @param DibiConnection $connection
	 */
	public function __construct(DibiConnection $connection) {
		$this->connection = $connection;
	}

	public function getNamespaces() {
		static $cache = null;

		if ($cache === null) {
			$cache = $this->connection->fetchPairs('
				SELECT tgn.namespace_id, tgn.name
				FROM gallery_namespace AS tgn
			');
		}

		return $cache;
	}

	public function createGroup(array $group_data) {
		$this->connection->begin();

		$this->connection->query('INSERT INTO gallery %v', $group_data, '');
		$group_id = $this->connection->insertId();

		$this->connection->commit();

		return $group_id;
	}

	public function updateGroup($group_id, array $update_data = array()) {
		$this->connection->begin();

		if ($update_data) {
			$this->connection->query('UPDATE gallery SET ', $update_data, 'WHERE gallery_id = %s', $group_id);
		}

		$this->connection->commit();
	}

	public function toggleActiveGroup($group_id) {
		$this->connection->begin();

		$is_active = $this->connection->fetchSingle('
			SELECT tg.is_active
			FROM gallery AS tg
			WHERE tg.gallery_id = %s', $group_id, '
			LIMIT 1
		');

		$is_active = $is_active ? false : true;

		$this->connection->query('
			UPDATE gallery
			SET is_active = %b', $is_active, '
			WHERE gallery_id = %s', $group_id, '
		');

		$this->connection->commit();
	}

	public function deleteGroup($group_id) {
		$this->connection->query('
			DELETE FROM gallery
			WHERE gallery_id = %s', $group_id, '
		');
	}

	public function getGroupCount($namespace_id, $admin = false) {
		return $this->connection->fetchSingle('
			SELECT COUNT(*)
			FROM gallery AS tg
			WHERE (
				SELECT COUNT(*)
				FROM gallery_photo AS tgp
				WHERE tgp.gallery_id = tg.gallery_id %SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			) > 0
			%sql', array('AND tg.namespace_id = %s', $namespace_id), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
		');
	}

	public function getAllGroups($namespace_id, $admin = false, $page = 1, $itemPerPage = 25) {
		$limit = $itemPerPage;
		$offset = ($page - 1) * $itemPerPage;

		$group_array = $this->connection->fetchAll('
			SELECT
				tg.gallery_id,
				tg.namespace_id,
				tgn.name AS namespace,
				tg.is_active,
				tg.title,
				tg.description,
				(
					SELECT tgp.filename FROM gallery_photo AS tgp
					WHERE tgp.gallery_id = tg.gallery_id %SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
					ORDER BY tgp.ordering ASC
					LIMIT 1
				) AS title_filename,
				(
					SELECT COUNT(*)
					FROM gallery_photo AS tgp
					WHERE tgp.gallery_id = tg.gallery_id %SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
				) AS photo_count
			FROM gallery AS tg
			LEFT JOIN gallery_namespace AS tgn ON (tgn.namespace_id = tg.namespace_id)
			WHERE %sql', array('tg.namespace_id = %s', $namespace_id), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
			HAVING photo_count > 0
			%lmt', $limit, ' %ofs', $offset, '
		');
		return $group_array;
	}

	public function getGroupById($group_id) {
		return $this->connection->fetch('
			SELECT
				tg.gallery_id, tg.namespace_id, tg.is_active, tg.title, tg.description,
				tgn.name AS namespace
			FROM gallery AS tg
			LEFT JOIN gallery_namespace AS tgn ON (tgn.namespace_id = tg.namespace_id)
			WHERE tg.gallery_id = %s', $group_id, '
			LIMIT 1
		');
	}
	
	public function createItem(array $item_data, $group_id) {
		$this->connection->begin();
		
		// Counted values
		$item_data['ordering'] = 1 + (int) $this->connection->fetchSingle('
			SELECT MAX(tgp.ordering) FROM gallery_photo AS tgp WHERE tgp.gallery_id = %s', $group_id, '
		');

		$this->connection->query('INSERT INTO gallery_photo %v', $item_data, '');
		$item_id = $this->connection->insertId();

		$this->connection->commit();
		
		return $item_id;
	}
	
	public function updateItem($item_id, array $update_data = array()) {
		$this->connection->begin();

		if ($update_data) {
			$this->connection->query('UPDATE gallery_photo SET', $update_data, 'WHERE photo_id = %s', $item_id);
		}

		$this->connection->commit();
	}
	
	public function toggleActiveItem($item_id) {
		$this->connection->begin();

		$is_active = $this->connection->fetchSingle('
			SELECT tgp.is_active
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id = %s', $item_id, '
			LIMIT 1
		');

		$is_active = $is_active ? false : true;

		$this->connection->query('
			UPDATE gallery_photo
			SET is_active = %b', $is_active, '
			WHERE photo_id = %s', $item_id, '
		');

		$this->connection->commit();
	}
	
	public function deleteItem($item_id) {
		$this->connection->query('
			DELETE FROM gallery_photo
			WHERE photo_id = %s', $item_id, '
		');
	}
	
	public function getItemsByGroup($group_id, $admin = false) {
		$photo_array = $this->connection->fetchAll('
			SELECT
				tgp.photo_id,
				tgp.is_active,
				tgp.gallery_id,
				tgp.filename,
				tgp.title
			FROM gallery_photo AS tgp
			WHERE tgp.gallery_id = %s', $group_id, '
				%SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			ORDER BY tgp.ordering
		');
		return $photo_array;
	}
	
	public function getItemById($item_id) {
		return $this->connection->fetch('
			SELECT tgp.photo_id, tgp.is_active, tgp.gallery_id, tgp.filename, tgp.title
			FROM gallery_photo AS tgp
			LEFT JOIN gallery AS tg ON (tg.gallery_id = tgp.gallery_id)
			WHERE tgp.photo_id = %s', $item_id, '
			LIMIT 1
		');
	}
	
	public function getLeftItemBy($item_id) {
		return $this->connection->fetchSingle('
			SELECT tgp.photo_id
			FROM gallery_photo AS tgp
			WHERE tgp.ordering < (
				SELECT tgp2.ordering
				FROM gallery_photo AS tgp2
				WHERE tgp2.photo_id = %s', $item_id, '
					AND tgp2.gallery_id = tgp.gallery_id
				LIMIT 1
			)
			ORDER BY tgp.ordering DESC
			LIMIT 1
		');
	}
	
	public function getRightItemBy($item_id) {
		return $this->connection->fetchSingle('
			SELECT tgp.photo_id
			FROM gallery_photo AS tgp
			WHERE tgp.ordering > (
				SELECT tgp2.ordering
				FROM gallery_photo AS tgp2
				WHERE tgp2.photo_id = %s', $item_id, '
					AND tgp2.gallery_id = tgp.gallery_id
				LIMIT 1
			)
			ORDER BY tgp.ordering ASC
			LIMIT 1
		');
	}
	
	public function swapItems($item_id_1, $item_id_2) {
		$this->connection->begin();

		$orderings = (array) $this->connection->fetchPairs('
			SELECT tgp.photo_id, tgp.ordering
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id IN %l', array($item_id_1, $item_id_2,), '
		');

		$this->connection->query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$item_id_2], '
			WHERE photo_id = %s', $item_id_1, '
		');

		$this->connection->query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$item_id_1], '
			WHERE photo_id = %s', $item_id_2, '
		');

		$this->connection->commit();
	}
	
}
