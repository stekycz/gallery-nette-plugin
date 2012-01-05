<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Photo extends AbstractItem {

	public function create(array $data) {
		$insert_data = array(
			'is_active' => true,
		);

		foreach ($data as $key => $value) {
			if (!$value) {
				unset($data[$key]);
			} elseif ($key != $this->environment->fileKey) {
				$insert_data[$key] = $value;
			}
		}

		/* @var $difference array */
		if (!($difference = array_diff(static::$basicColumns, array_keys($insert_data)))) {
			throw new InvalidStateException('Missing required fields ['.implode(', ', $difference).'].');
		}
		
		$file = $data[$this->environment->fileKey];
		if (!$file->isImage()) {
			throw new InvalidArgumentException('Given file is not image. It is [' . $file->getContentType() . '].');
		}

		// Save image
		$extension = $this->detectExtension($file);
		$filename = sha1($file->name) . '.' . $extension;
		$filepath = $this->getPathImage($data['gallery_id'], $filename);
		$file->move($filepath);

		// Database save
		$this->database->begin();
		
		// Counted values
		$insert_data['filename'] = $filename;
		$insert_data['ordering'] = 1 + (int) $this->database->fetchSingle('
			SELECT MAX(tgp.ordering) FROM gallery_photo AS tgp WHERE tgp.gallery_id = %s', $data['gallery_id'], '
		');

		$this->database->query('INSERT INTO gallery_photo %v', $insert_data, '');
		$photo_id = $this->database->insertId();

		$this->database->commit();
		
		return $photo_id;
	}

	/**
	 * Updates photo. It means only extended info. New image should be created, not updated.
	 * 
	 * @param array $data
	 */
	public function update(array $data) {
		if (!array_key_exists('photo_id', $data)) {
			throw new InvalidArgumentException('Given data do not contain photo_id.');
		}

		$photo_id = $data['photo_id'];
		$previous_data = $this->getById($photo_id);
		$update_data = array();

		foreach ($data as $key => $value) {
			if ($key != $this->environment->fileKey && $previous_data[$key] != $value) {
				$update_data[$key] = $value;
			}
		}
		
		// Database save
		$this->database->begin();

		if ($update_data) {
			$this->database->query('UPDATE gallery_photo SET', $update_data, 'WHERE photo_id = %s', $photo_id);
		}

		$this->database->commit();
		
		return $photo_id;
	}

	/**
	 * Detects extension by file content type. (Only for images.)
	 * 
	 * @param HttpUploadedFile $file
	 * @return string Extension (without dot)
	 */
	protected function detectExtension(HttpUploadedFile $file) {
		$content_type = $file->getContentType();
		switch ($content_type) {
			case 'image/gif':
				return 'gif';
				break;
			case 'image/png':
				return 'png';
				break;
			case 'image/jpeg':
				return 'jpg';
				break;
			default:
				throw new InvalidArgumentException('Given file [' . $content_type . '] is not supported image type.');
		}
	}

	public function getPathImage($id, $filename) {
		return $this->environment->groupModel->getPathGallery($id) . '/' . $filename;
	}

	public function toggleActive($id) {
		$this->database->begin();

		$is_active = $this->database->fetchSingle('
			SELECT tgp.is_active
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');

		$is_active = $is_active ? false : true;

		$this->database->query('
			UPDATE gallery_photo
			SET is_active = %b', $is_active, '
			WHERE photo_id = %s', $id, '
		');

		$this->database->commit();
	}

	public function delete($id) {
		$this->deleteFile($id);

		$this->database->query('
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
		$result = $this->database->fetch('
			SELECT tgp.filename, tgp.gallery_id
			FROM gallery_photo AS tgp
			LEFT JOIN gallery AS tg ON (tg.gallery_id = tgp.gallery_id)
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');

		if (!$result) {
			throw new InvalidArgumentException('Photo with ID [' . $id . '] was not found.');
		}
		$filename = $result['filename'];
		$gallery_id = $result['gallery_id'];

		$filepath_regular = $this->getPathImage($gallery_id, $filename);

		if (file_exists($filepath_regular) && is_file($filepath_regular)) {
			unlink($filepath_regular);
		}
	}

	public function moveLeft($id) {
		$left_id = $this->database->fetchSingle('
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

	public function moveRight($id) {
		$right_id = $this->database->fetchSingle('
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
		$this->database->begin();

		$orderings = (array) $this->database->fetchPairs('
			SELECT tgp.photo_id, tgp.ordering
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id IN %l', array($photo_id_1, $photo_id_2,), '
		');

		$this->database->query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$photo_id_2], '
			WHERE photo_id = %s', $photo_id_1, '
		');

		$this->database->query('
			UPDATE gallery_photo
			SET ordering = %s', $orderings[$photo_id_1], '
			WHERE photo_id = %s', $photo_id_2, '
		');

		$this->database->commit();
	}

	public function getByGallery($id, $admin = false) {
		$photo_array = $this->database->fetchAll('
			SELECT
				tgp.photo_id,
				tgp.is_active,
				tgp.gallery_id,
				tgp.filename,
				tgp.title
			FROM gallery_photo AS tgp
			WHERE tgp.gallery_id = %s', $id, '
				%SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			ORDER BY tgp.ordering
		');
		return $photo_array;
	}

	public function getById($id) {
		return $this->database->fetch('
			SELECT tgp.photo_id, tgp.is_active, tgp.title
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');
	}

}
