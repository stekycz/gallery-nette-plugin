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

namespace stekycz\gallery\Model;

use \Nette\InvalidStateException;
use \Nette\InvalidArgumentException;
use \Nette\Http\FileUpload;

/**
 * Contains basic implementation for item model.
 */
class Item extends AbstractItem {

	public function create(array $data) {
		$insert_data = array(
			'is_active' => true,
		);

		foreach ($data as $key => $value) {
			if (!$value) {
				unset($data[$key]);
			} elseif ($key != static::FILE_KEY) {
				$insert_data[$key] = $value;
			}
		}

		/* @var $difference array */
		if (!($difference = array_diff(static::$basicColumns, array_keys($insert_data)))) {
			throw new InvalidStateException('Missing required fields ['.implode(', ', $difference).'].');
		}

		$file = $data[static::FILE_KEY];
		if (!$file->isImage()) {
			throw new InvalidArgumentException('Given file is not image. It is [' . $file->getContentType() . '].');
		}

		// Save image
		$extension = $this->detectExtension($file);
		$filename = sha1($file->name) . '.' . $extension;
		$filepath = $this->getPathImage($data['gallery_id'], $filename);
		$file->move($filepath);

		$insert_data['filename'] = $filename;

		$photo_id = $this->dataProvider->createItem($insert_data, $data['gallery_id']);

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
			if ($key != static::FILE_KEY && $previous_data[$key] != $value) {
				$update_data[$key] = $value;
			}
		}

		$this->dataProvider->updateItem($photo_id, $update_data);

		return $photo_id;
	}

	/**
	 * Detects extension by file content type. (Only for images.)
	 *
	 * @param \Nette\Http\FileUpload $file
	 * @return string Extension (without dot)
	 */
	protected function detectExtension(FileUpload $file) {
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
		$groupModel = new Group($this->dataProvider, $this->basePath);
		$group_row = $this->dataProvider->getGroupById($id);
		$groupModel->useNamespace($group_row['namespace_id']);
		return $groupModel->getPathGallery($id) . '/' . $filename;
	}

	public function toggleActive($id) {
		$this->dataProvider->toggleActiveItem($id);
	}

	public function delete($id) {
		$this->deleteFile($id);
		$this->dataProvider->deleteItem($id);
	}

	/**
	 * Deletes photo file.
	 *
	 * @param int $id Photo ID
	 */
	protected function deleteFile($id) {
		$result = $this->dataProvider->getItemById($id);

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
		$left_id = $this->dataProvider->getLeftItemBy($id);
		$this->swapPhotos($id, $left_id);
	}

	public function moveRight($id) {
		$right_id = $this->dataProvider->getRightItemBy($id);
		$this->swapPhotos($id, $right_id);
	}

	/**
	 * Swaps ordering between given photos.
	 *
	 * @param int $photo_id_1 Photo ID
	 * @param int $photo_id_2 Photo ID
	 */
	protected function swapPhotos($photo_id_1, $photo_id_2) {
		$this->dataProvider->swapItems($photo_id_1, $photo_id_2);
	}

	public function getByGallery($id, $admin = false) {
		return $this->dataProvider->getItemsByGroup($id, $admin);
	}

	public function getById($id) {
		return $this->dataProvider->getItemById($id);
	}

}
