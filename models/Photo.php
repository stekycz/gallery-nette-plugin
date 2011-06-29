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
		$extended_data = array();

		foreach ($data as $key => $value) {
			if (!$value) {
				unset($data[$key]);
			} elseif (in_array($key, self::$basicColumns)) {
				$insert_data[$key] = $value;
			} elseif ($key != $this->environment->fileKey) {
				$extended_data[$key] = $value;
			}
		}

		$file = $data[$this->environment->fileKey];
		if (!$file->isImage()) {
			throw new InvalidArgumentException('Given file is not image. It is [' . $file->getContentType() . '].');
		}

		// Save image
		$extension = $this->detectExtension($file);
		$filename = sha1($file->name) . '.' . $extension;
		$filepath = $this->environment->basePath . '/' . $data['gallery_id'] . '/' . $filename;
		$file->move($filepath);
		$image = $file->toImage();
		
		// Make it smaller
		$image->resize($this->environment->imageSize, $this->environment->imageSize);
		$image->save($filepath, $this->environment->imageQuality);

		// Make thumbnail
		$image->resize($this->environment->thumbnailWidth, $this->environment->thumbnailWidth, Image::FILL);
		$top = ($image->getHeight() - $this->environment->thumbnailHeight) / 2;
		$left = ($image->getWidth() - $this->environment->thumbnailWidth) / 2;
		if ($top > 0 || $left > 0) {
			$image->crop($left > 0 ? $left : 0, $top > 0 ? $top : 0, $this->environment->thumbnailWidth, $this->environment->thumbnailHeight);
		}
		$image->save($this->environment->basePath . '/' . $data['gallery_id'] . '/' . $this->environment->thumbnailsDirName . '/' . $filename);

		// Counted values
		$insert_data['filename'] = $filename;
		$insert_data['ordering'] = 1 + (int) dibi::fetchSingle('
			SELECT MAX(tgp.ordering) FROM gallery_photo AS tgp WHERE tgp.gallery_id = %s', $data['gallery_id'], '
		');
		
		// Database save
		dibi::begin();

		dibi::query('INSERT INTO gallery_photo %v', $insert_data, '');
		$photo_id = dibi::insertId();

		if ($extended_data) {
			$this->insertExtendedData($extended_data, $photo_id);
		}

		dibi::commit();
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
		$extended_data = array();

		foreach ($data as $key => $value) {
			if ($key != $this->environment->fileKey && $previous_data[$key] != $value) {
				if (!in_array($key, self::$basicColumns)) {
					$extended_data[$key] = $value;
				}
			}
		}
		
		// Database save
		dibi::begin();

		$previous_extended_exists = dibi::fetch('SELECT 1 FROM gallery_photo_extended WHERE photo_id = %s', $photo_id, '');
		if ($previous_extended_exists && $extended_data) {
			dibi::query('UPDATE gallery_photo_extended SET', $extended_data, 'WHERE photo_id = %s', $photo_id);
		} elseif (!$previous_extended_exists && $extended_data) {
			$this->insertExtendedData($extended_data, $photo_id);
		}

		dibi::commit();
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
	
	/**
	 * Inserts extended data about photo into database.
	 * 
	 * @param array $extended_data
	 * @param int $photo_id
	 */
	protected function insertExtendedData(array $extended_data, $photo_id) {
		$extended_data['photo_id'] = $photo_id;
		dibi::query('INSERT INTO gallery_photo_extended %v', $extended_data, '');
	}

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
		$result = dibi::fetchAll('
			SELECT tgp.filename, tgp.gallery_id
			FROM gallery_photo AS tgp
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');

		if (!$result) {
			throw new InvalidArgumentException('Photo with ID [' . $id . '] was not found.');
		}
		$filename = $result[0]['filename'];
		$gallery_id = $result[0]['gallery_id'];

		$filepath_thumbnails = $this->environment->basePath . '/' . $gallery_id . '/' . $this->environment->thumbnailsDirName . '/' . $filename;
		$filepath_regular = $this->environment->basePath . '/' . $gallery_id . '/' . $filename;

		if (file_exists($filepath_thumbnails) && is_file($filepath_thumbnails)) {
			unlink($filepath_thumbnails);
		}
		if (file_exists($filepath_regular) && is_file($filepath_regular)) {
			unlink($filepath_regular);
		}
	}
	
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
			WHERE tgp.photo_id IN %l', array($photo_id_1, $photo_id_2,), '
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

	public function getByGallery($id, $admin = false) {
		$photo_array = dibi::fetchAll('
			SELECT
				tgp.photo_id,
				tgp.is_active,
				tgp.gallery_id,
				tgp.filename,
				tgpe.title
			FROM gallery_photo AS tgp
			LEFT JOIN gallery_photo_extended AS tgpe ON (tgpe.photo_id = tgp.photo_id)
			WHERE tgp.gallery_id = %s', $id, '
				%SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			ORDER BY tgp.ordering
		');
		return $photo_array;
	}

	public function getById($id) {
		return dibi::fetch('
			SELECT tgp.photo_id, tgp.is_active, tgpe.title
			FROM gallery_photo AS tgp
			LEFT JOIN gallery_photo_extended AS tgpe ON (tgpe.photo_id = tgp.photo_id)
			WHERE tgp.photo_id = %s', $id, '
			LIMIT 1
		');
	}
	
}
