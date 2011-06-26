<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Gallery extends Object implements IGallery {
	
	/**
	 * Creates new gallery.
	 */
	public function create() {
		throw new NotImplementedException();
	}
	
	/**
	 * Updates gallery.
	 */
	public function update() {
		throw new NotImplementedException();
	}
	
	/**
	 * Toggles activity/visibility of gallery.
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
		
		$is_active = $is_active ? 0 : 1;
		
		dibi::query('
			UPDATE gallery
			SET is_active = %s', $is_active, '
			WHERE gallery_id = %s', $id, '
		');
		
		dibi::commit();
	}
	
	/**
	 * Deletes gallery.
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
	 * Deletes whole folder of gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	protected function deleteFolder($id) {
		throw new NotImplementedException();
	}
	
	/**
	 * Returns all galleries which are not deleted. If admin is true returns 
	 * invisible galleries too.
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
					WHERE tgp.gallery_id = tg.gallery_id AND tgp.is_active = 1
					ORDER BY tgp.ordering ASC
					LIMIT 1
				) AS title_filename,
				(SELECT COUNT(*) FROM gallery_photo AS tgp WHERE tgp.is_active = 1) AS photo_count
			FROM gallery AS tg
			LEFT JOIN gallery_extended AS tge ON (tge.gallery_id = tg.gallery_id)
			%SQL', (!$admin ? 'WHERE tg.is_active = 1' : ''), '
			HAVING photo_count > 0
		');
		return $gallery_array;
	}
	
	/**
	 * Returns base uri for gallery files.
	 * 
	 * @return string
	 */
	public function getBaseUri() {
		return Environment::getHttpRequest()->url->baseUrl . '/files/gallery';
	}
	
}
