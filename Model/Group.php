<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011.06.26
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace steky\nette\gallery\Model;
use \Nette\InvalidStateException,
	\Nette\InvalidArgumentException;

/**
 * Contains basic implementation for group model.
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
			} elseif ($key != static::FORM_FILES_KEY) {
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

		if (isset($data[static::FORM_FILES_KEY])) {
			$files = $data[static::FORM_FILES_KEY];
		} else {
			throw new InvalidArgumentException('You should not inicialize gallery without any photos.');
		}

		$gallery_id = $this->dataProvider->createGroup($insert_data);
		$this->insertFiles($files, $gallery_id);

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
			if ($key != static::FORM_FILES_KEY && $previous_data[$key] != $value) {
				$update_data[$key] = $value;
			}
		}

		$files = array();
		if (isset($data[static::FORM_FILES_KEY])) {
			$files = $data[static::FORM_FILES_KEY];
		}

		$this->dataProvider->updateGroup($gallery_id, $update_data);
		if ($files) {
			$this->insertFiles($files, $gallery_id);
		}

		return $gallery_id;
	}

	/**
	 * Inserts given files into group by group_id.
	 *
	 * @param array $files
	 * @param int $group_id
	 */
	protected function insertFiles(array $files, $group_id) {
		$files_data = array(
			'gallery_id' => $group_id,
		);

		$itemModel = new Item($this->dataProvider, $this->basePath);
		foreach ($files as $file) {
			$files_data[AbstractItem::FILE_KEY] = $file;
			$itemModel->create($files_data);
		}
	}

	public function toggleActive($id) {
		$this->dataProvider->toggleActiveGroup($id);
	}

	public function delete($id) {
		$this->deleteFolder($id);
		$this->dataProvider->deleteGroup($id);
	}

	/**
	 * Deletes whole folder of group.
	 *
	 * @param int $id Gallery ID
	 */
	protected function deleteFolder($id) {
		$itemModel = new Item($this->dataProvider, $this->basePath);
		$photos = $itemModel->getByGallery($id, true);
		foreach ($photos as $photo) {
			$itemModel->delete($photo['photo_id']);
		}

		$regular_dir_path = $this->getPathGallery($id);
		if (is_dir($regular_dir_path)) {
			rmdir($regular_dir_path);
		}
	}

	public function getPathNamespace() {
		return $this->basePath . '/' . $this->getCurrentNamespaceName();
	}

	protected function getCurrentNamespaceName() {
		return $this->dataProvider->namespaces[$this->namespace_id];
	}

	public function getPathGallery($id) {
		return $this->getPathNamespace() . '/' . $id;
	}

	public function getCount($admin = false) {
		return $this->dataProvider->getGroupCount($this->namespace_id, $admin);
	}

	public function getAll($page = 1, $itemPerPage = 25, $admin = false) {
		return $this->dataProvider->getAllGroups($this->namespace_id, $admin, $page, $itemPerPage);
	}

	public function getById($id) {
		return $this->dataProvider->getGroupById($id);
	}

}
