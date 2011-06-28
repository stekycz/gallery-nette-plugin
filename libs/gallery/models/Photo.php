<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Photo extends AbstractItem {
	
	/**
	 * Creates new photo.
	 */
	public function create(array $data) {
		throw new NotImplementedException();
	}
	
	/**
	 * Updates photo.
	 */
	public function update(array $data) {
		throw new NotImplementedException();
	}
	
	/**
	 * Toggles activity/visibility of photo.
	 * 
	 * @param int $id Photo ID
	 */
	public function toggleActive($id) {
		dibi::begin();
		
		$is_active = dibi::fetchSingle('
			SELECT tgp.is_active
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');
		
		$is_active = $is_active ? false : true;
		
		dibi::query('
			UPDATE gallery_photo
			SET is_active = %b', $is_active, '
			WHERE photo_id = %s', $id, '
		');
		
		dibi::commit();
	}
	
	/**
	 * Deletes photo.
	 * 
	 * @param int $id Photo ID
	 */
	public function delete($id) {
		$this->deleteFile($id);
		
		dibi::query('
			DELETE FROM gallery_photo
			WHERE photo_id = %s', $id, '
		');
	}
	
	/**
	 * Deletes photo file.
	 * 
	 * @param int $id Photo ID
	 */
	protected function deleteFile($id) {
		throw new NotImplementedException();
	}
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id Photo ID 
	 */
	public function moveLeft($id) {
		$left_id = dibi::fetchSingle('
			SELECT tgp.photo_id
			FROM gallery_photo AS tgp
			WHERE tgp.ordering < (
				SELECT tgp2.ordering
				FROM gallery_photo AS tgp2
				WHERE tgp2.photo_id = %s', $id, '
					AND tgp2.gallery_id = tgp.gallery_id
				LIMIT 1
			)
			ORDER BY tgp.ordering DESC
			LIMIT 1
		');
		$this->swapPhotos($id, $left_id);
	}

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id Photo ID 
	 */
	public function moveRight($id) {
		$right_id = dibi::fetchSingle('
			SELECT tgp.photo_id
			FROM gallery_photo AS tgp
			WHERE tgp.ordering > (
				SELECT tgp2.ordering
				FROM gallery_photo AS tgp2
				WHERE tgp2.photo_id = %s', $id, '
					AND tgp2.gallery_id = tgp.gallery_id
				LIMIT 1
			)
			ORDER BY tgp.ordering ASC
			LIMIT 1
		');
		$this->swapPhotos($id, $right_id);
	}
	
	/**
	 * Swaps ordering between given photos.
	 * 
	 * @param int $photo_id_1 Photo ID
	 * @param int $photo_id_2 Photo ID
	 */
	protected function swapPhotos($photo_id_1, $photo_id_2) {
		dibi::begin();
		
		$orderings = (array) dibi::fetchPairs('
			SELECT tgp.photo_id, tgp.ordering
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id IN %l', array($photo_id_1, $photo_id_2, ), '
		');
		
		dibi::query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$photo_id_2], '
			WHERE photo_id = %s', $photo_id_1, '
		');
		
		dibi::query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$photo_id_1], '
			WHERE photo_id = %s', $photo_id_2, '
		');
		
		dibi::commit();
	}
	
	/**
	 * Returns all photos in gallery. If admin is true 
	 * returns invisible photos too.
	 * 
	 * @param int $id
	 * @param bool $admin
	 * @return DibiResult
	 */
	public function getByGallery($id, $admin = false) {
		$photo_array = dibi::fetchAll('
			SELECT
				tgp.photo_id,
				tgp.is_active,
				tgp.gallery_id,
				tgp.filename
			FROM gallery_photo AS tgp
			WHERE tgp.gallery_id = %s', $id, '
				%SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			ORDER BY tgp.ordering
		');
		return $photo_array;
	}
	
}
