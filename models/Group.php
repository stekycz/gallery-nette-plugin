<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Group extends AbstractGroup {

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
			} elseif ($key != $this->environment->formFilesKey) {
				$extended_data[$key] = $value;
			}
		}
		
		if ($this->namespace !== null) {
			$insert_data['namespace'] = $this->namespace;
		}

		dibi::begin();

		dibi::query('INSERT INTO gallery %v', $insert_data, '');
		$gallery_id = dibi::insertId();

		if ($extended_data) {
			$this->insertExtendedData($extended_data, $gallery_id);
		}
		
		if (isset($data[$this->environment->formFilesKey])) {
			$this->insertFiles($data[$this->environment->formFilesKey], $gallery_id);
		} else {
			throw new InvalidArgumentException('You should not inicialize gallery without any photos.');
		}

		dibi::commit();
	}

	/**
	 * Updates group. It means add photos and change extended info. If extended
	 * info does not exist it will be inserted.
	 * 
	 * @param array $data
	 */
	public function update(array $data) {
		if (!array_key_exists('gallery_id', $data)) {
			throw new InvalidArgumentException('Given data do not contain gallery_id.');
		}
		
		$gallery_id = $data['gallery_id'];
		$previous_data = $this->getById($gallery_id);
		$extended_data = array();

		foreach ($data as $key => $value) {
			if ($key != $this->environment->formFilesKey && $previous_data[$key] != $value) {
				if (!in_array($key, self::$basicColumns)) {
					$extended_data[$key] = $value;
				}
			}
		}

		dibi::begin();

		$previous_extended_exists = dibi::fetch('SELECT 1 FROM gallery_extended WHERE gallery_id = %s', $gallery_id, '');
		if ($previous_extended_exists && $extended_data) {
			dibi::query('UPDATE gallery_extended SET', $extended_data, 'WHERE gallery_id = %s', $gallery_id);
		} elseif (!$previous_extended_exists && $extended_data) {
			$this->insertExtendedData($extended_data, $gallery_id);
		}
		
		if (isset($data[$this->environment->formFilesKey])) {
			$this->insertFiles($data[$this->environment->formFilesKey], $gallery_id);
		}

		dibi::commit();
	}
	
	/**
	 * Inserts given files into gallery by gallery_id.
	 * 
	 * @param array $files
	 * @param int $gallery_id
	 */
	protected function insertFiles(array $files, $gallery_id) {
		if ($this->namespace !== null) {
			$basePath = $this->environment->basePath . '/' . $this->namespace;
		} else {
			$basePath = $this->environment->basePath;
		}
		
		// For future thumbnails
		$thumbnails_dir_path = $basePath . '/' . $gallery_id . '/' . $this->environment->thumbnailsDirName;
		if (!file_exists($thumbnails_dir_path)) {
			mkdir($thumbnails_dir_path, 0777, true);
		}
		
		$files_data = array(
			'gallery_id' => $gallery_id,
		);
		
		if ($this->namespace !== null) {
			$files_data['namespace'] = $this->namespace;
		}
		
		foreach ($files as $file) {
			$files_data[$this->environment->fileKey] = $file;
			$this->environment->itemModel->create($files_data);
		}
	}
	
	/**
	 * Inserts extended data about gallery into database.
	 * 
	 * @param array $extended_data
	 * @param int $gallery_id
	 */
	protected function insertExtendedData(array $extended_data, $gallery_id) {
		$extended_data['gallery_id'] = $gallery_id;
		dibi::query('INSERT INTO gallery_extended %v', $extended_data, '');
	}

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
		$photos = $this->environment->itemModel->getByGallery($id, true);
		foreach ($photos as $photo) {
			$this->environment->itemModel->delete($photo['photo_id']);
		}
		
		$gallery = $this->environment->groupModel->getById($id);
		$namespace = $gallery['namespace'];
		
		if ($namespace !== null) {
			$basePath = $this->environment->basePath . '/' . $namespace;
		} else {
			$basePath = $this->environment->basePath;
		}
		
		$regular_dir_path = $basePath . '/' . $id;
		$thumbnails_dir_path = $basePath . '/' . $id . '/' . $this->environment->thumbnailsDirName;
		// Thumbnail folder is in regular -> must be deleted first
		if (is_dir($thumbnails_dir_path)) {
			rmdir($thumbnails_dir_path);
		}
		if (is_dir($regular_dir_path)) {
			rmdir($regular_dir_path);
		}
	}
	
	public function getCount($admin = false) {
		return dibi::fetchSingle('
			SELECT COUNT(*)
			FROM gallery AS tg
			WHERE (
				SELECT COUNT(*)
				FROM gallery_photo AS tgp
				WHERE tgp.gallery_id = tg.gallery_id %SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			) > 0
			%sql', ($this->namespace
						? array('AND tg.namespace = %s', $this->namespace)
						: 'AND tg.namespace IS NULL'
			), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
		');
	}

	public function getAll($page = 1, $itemPerPage = 25, $admin = false) {
		$limit = $itemPerPage;
		$offset = ($page - 1) * $itemPerPage;
		
		$gallery_array = dibi::fetchAll('
			SELECT
				tg.gallery_id,
				tg.namespace,
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
			WHERE %sql', ($this->namespace
						? array('tg.namespace = %s', $this->namespace)
						: 'tg.namespace IS NULL'
			), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
			HAVING photo_count > 0
			%lmt', $limit, ' %ofs', $offset, '
		');
		return $gallery_array;
	}
	
	public function getById($id) {
		return dibi::fetch('
			SELECT tg.gallery_id, tg.namespace, tg.is_active, tge.title, tge.description
			FROM gallery AS tg
			LEFT JOIN gallery_extended AS tge ON (tge.gallery_id = tg.gallery_id)
			WHERE tg.gallery_id = %s', $id, '
			LIMIT 1
		');
	}

}
