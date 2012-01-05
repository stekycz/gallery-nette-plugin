<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class Group extends AbstractGroup {

	public function create(array $data) {
		$insert_data = array(
			'is_active' => true,
			'namespace_id' => static::DEFAULT_NAMESPACE_ID,
		);

		foreach ($data as $key => $value) {
			if (!$value) {
				unset($data[$key]);
			} elseif ($key != $this->environment->formFilesKey) {
				$insert_data[$key] = $value;
			}
		}
		
		/* @var $difference array */
		if (!($difference = array_diff(static::$basicColumns, array_keys($insert_data)))) {
			throw new InvalidStateException('Missing required fields ['.implode(', ', $difference).'].');
		}
		
		if ($this->namespace_id != static::DEFAULT_NAMESPACE_ID) {
			$insert_data['namespace_id'] = $this->namespace_id;
		}

		$this->database->begin();

		$this->database->query('INSERT INTO gallery %v', $insert_data, '');
		$gallery_id = $this->database->insertId();
		
		if (isset($data[$this->environment->formFilesKey])) {
			$this->insertFiles($data[$this->environment->formFilesKey], $gallery_id);
		} else {
			throw new InvalidArgumentException('You should not inicialize gallery without any photos.');
		}

		$this->database->commit();
		
		return $gallery_id;
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
		$update_data = array();
		
		foreach ($data as $key => $value) {
			if ($key != $this->environment->formFilesKey && $previous_data[$key] != $value) {
				$update_data[$key] = $value;
			}
		}

		$this->database->begin();
		
		if ($update_data) {
			$this->database->query('UPDATE gallery SET ', $update_data, 'WHERE gallery_id = %s', $gallery_id);
		}
		
		if (isset($data[$this->environment->formFilesKey])) {
			$this->insertFiles($data[$this->environment->formFilesKey], $gallery_id);
		}

		$this->database->commit();
		
		return $gallery_id;
	}
	
	/**
	 * Inserts given files into gallery by gallery_id.
	 * 
	 * @param array $files
	 * @param int $gallery_id
	 */
	protected function insertFiles(array $files, $gallery_id) {
		$files_data = array(
			'gallery_id' => $gallery_id,
		);
		
		foreach ($files as $file) {
			$files_data[$this->environment->fileKey] = $file;
			$this->environment->itemModel->create($files_data);
		}
	}

	public function toggleActive($id) {
		$this->database->begin();

		$is_active = $this->database->fetchSingle('
			SELECT tg.is_active
			FROM gallery AS tg
			WHERE tg.gallery_id = %s', $id, '
			LIMIT 1
		');

		$is_active = $is_active ? false : true;

		$this->database->query('
			UPDATE gallery
			SET is_active = %b', $is_active, '
			WHERE gallery_id = %s', $id, '
		');

		$this->database->commit();
	}

	public function delete($id) {
		$this->deleteFolder($id);

		$this->database->query('
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
		$namespace_id = $gallery['namespace_id'];
		
		$regular_dir_path = $this->getPathGallery($id);
		if (is_dir($regular_dir_path)) {
			rmdir($regular_dir_path);
		}
	}
	
	public function getPathNamespace() {
		if ($this->namespace_id === static::DEFAULT_NAMESPACE_ID) {
			return $this->environment->basePath;
		} else {
			return $this->environment->basePath . '/' . $this->getCurrentNamespaceName();
		}
	}
	
	protected function getCurrentNamespaceName() {
		$namespaces = $this->environment->namespaces;
		return $namespaces[$this->namespace_id];
	}
	
	public function getPathGallery($id) {
		return $this->getPathNamespace() . '/' . $id;
	}
	
	public function getCount($admin = false) {
		return $this->database->fetchSingle('
			SELECT COUNT(*)
			FROM gallery AS tg
			WHERE (
				SELECT COUNT(*)
				FROM gallery_photo AS tgp
				WHERE tgp.gallery_id = tg.gallery_id %SQL', (!$admin ? 'AND tgp.is_active = 1' : ''), '
			) > 0
			%sql', array('AND tg.namespace_id = %s', $this->namespace_id), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
		');
	}

	public function getAll($page = 1, $itemPerPage = 25, $admin = false) {
		$limit = $itemPerPage;
		$offset = ($page - 1) * $itemPerPage;
		
		$gallery_array = $this->database->fetchAll('
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
			WHERE %sql', array('tg.namespace_id = %s', $this->namespace_id), '
			%SQL', (!$admin ? 'AND tg.is_active = 1' : ''), '
			HAVING photo_count > 0
			%lmt', $limit, ' %ofs', $offset, '
		');
		return $gallery_array;
	}
	
	public function getById($id) {
		return $this->database->fetch('
			SELECT
				tg.gallery_id, tg.namespace_id, tg.is_active, tg.title, tg.description,
				tgn.name AS namespace
			FROM gallery AS tg
			LEFT JOIN gallery_namespace AS tgn ON (tgn.namespace_id = tg.namespace_id)
			WHERE tg.gallery_id = %s', $id, '
			LIMIT 1
		');
	}

}
