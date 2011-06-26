<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Photo extends Object {
	
	/**
	 * Creates new photo.
	 */
	public static function create() {
		
	}
	
	/**
	 * Toggles activity/visibility of photo.
	 * 
	 * @param int $id Photo ID
	 */
	public static function toggleActive($id) {
		
	}
	
	/**
	 * Deletes photo.
	 * 
	 * @param int $id Photo ID
	 */
	public static function delete($id) {
		
	}
	
	/**
	 * Deletes photo file.
	 * 
	 * @param int $id Photo ID
	 */
	protected static function deleteFile($id) {
		
	}
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id Photo ID 
	 */
	public static function moveLeft($id) {
		
	}

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id Photo ID 
	 */
	public static function moveRight($id) {
		
	}
	
	/**
	 * Swaps ordering between given photos.
	 * 
	 * @param int $photo_id_1 Photo ID
	 * @param int $photo_id_2 Photo ID
	 */
	protected static function _swapPhotos($photo_id_1, $photo_id_2) {
		
	}
	
	/**
	 * Returns all photos in gallery. If admin is true 
	 * returns invisible photos too.
	 * 
	 * @param int $id
	 * @param bool $admin
	 * @return DibiResult
	 */
	public static function getByGallery($id, $admin = false) {
		$photo_array = dibi::fetchAll('
			SELECT
				tgp.photo_id,
				tgp.is_active,
				tgp.gallery_id,
				tgp.filename
			FROM gallery_photo AS tgp
			WHERE tgp.is_deleted = 0
				AND tgp.gallery_id = %s', $id, '
				%SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			ORDER BY tgp.ordering
		');
		return $photo_array;
	}
	
}
