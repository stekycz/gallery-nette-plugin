<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Group extends AbstractGroup {
	
	/**
	 * Creates new group.
	 */
	public function create(array $data) {
		throw new NotImplementedException();
	}
	
	/**
	 * Updates group.
	 */
	public function update(array $data) {
		throw new NotImplementedException();
	}
	
	/**
	 * Toggles activity/visibility of group.
	 * 
	 * @param int $id Gallery ID
	 */
	public function toggleActive($id) {
		dibi::begin();
		
		$is_active = dibi::fetchSingle('
			SELECT tg.is_active
			FROM gallery AS tg
			WHERE tg.gallery_id = %s', $id, '
			LIMIT 1
		');
		
		$is_active = $is_active ? false : true;
		
		dibi::query('
			UPDATE gallery
			SET is_active = %b', $is_active, '
			WHERE gallery_id = %s', $id, '
		');
		
		dibi::commit();
	}
	
	/**
	 * Deletes group.
	 * 
	 * @param int $id Gallery ID
	 */
	public function delete($id) {
		$this->deleteFolder($id);
		
		dibi::query('
			DELETE FROM gallery
			WHERE gallery_id = %s', $id, '
		');
	}
	
	/**
	 * Deletes whole folder of group.
	 * 
	 * @param int $id Gallery ID
	 */
	protected function deleteFolder($id) {
		throw new NotImplementedException();
	}
	
	/**
	 * Returns all groups which are not deleted. If admin is true returns 
	 * invisible groups too.
	 * 
	 * @param bool $admin
	 * @return DibiResult
	 */
	public function getAll($admin = false) {
		$gallery_array = dibi::fetchAll('
			SELECT
				tg.gallery_id,
				tg.is_active,
				tge.title,
				tge.description,
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
			LEFT JOIN gallery_extended AS tge ON (tge.gallery_id = tg.gallery_id)
			%SQL', (!$admin ? 'WHERE tg.is_active = 1' : ''), '
			HAVING photo_count > 0
		');
		return $gallery_array;
	}
	
}
