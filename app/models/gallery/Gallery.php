<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Gallery extends Object {
	
	/**
	 * Creates new gallery.
	 */
	public static function create() {
		
	}
	
	/**
	 * Toggles activity/visibility of gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	public static function toggleActive($id) {
		
	}
	
	/**
	 * Deletes gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	public static function delete($id) {
		
	}
	
	/**
	 * Deletes whole folder of gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	protected static function deleteFolder($id) {
		
	}
	
	/**
	 * Returns all galleries which are not deleted. If admin is true returns 
	 * invisible galleries too.
	 * 
	 * @param bool $admin
	 * @return DibiResult
	 */
	public static function getAll($admin = false) {
		$gallery_array = dibi::fetchAll('
			SELECT
				tg.gallery_id,
				tg.is_active,
				tge.title,
				tge.description,
				(
					SELECT tgp.filename FROM gallery_photo AS tgp
					WHERE tgp.gallery_id = tg.gallery_id AND tgp.is_deleted = 0 AND tgp.is_active = 1
					ORDER BY tgp.ordering ASC
					LIMIT 1
				) AS title_filename,
				(SELECT COUNT(*) FROM gallery_photo AS tgp WHERE tgp.is_active = 1 AND tgp.is_deleted = 0) AS photo_count
			FROM gallery AS tg
			LEFT JOIN gallery_extended AS tge ON (tge.gallery_id = tg.gallery_id)
			WHERE tg.is_deleted = 0
				%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
			HAVING photo_count > 0
		');
		return $gallery_array;
	}
	
}
