<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractItem extends AbstractGalleryModel {

	/**
	 * @var array Names of columns which are required
	 */
	protected static $basicColumns = array(
		'photo_id', 'gallery_id', 'filename', 'ordering', 'is_active',
	);
	/**
	 * @var AbstractGroup
	 */
	private static $instance = null;

	/**
	 * Returns instance.
	 * 
	 * @param GalleryEnvironment $environment
	 * @return AbstractGroup
	 */
	public static function getInstance(GalleryEnvironment $environment) {
		if (self::$instance === null) {
			$called_class = get_called_class();
			self::$instance = new $called_class($environment);
		}
		return self::$instance;
	}

	/**
	 * Returns path to image. Gallery is define by ID.
	 * 
	 * @param int $id Gallery ID
	 * @param string $filename Filename
	 * @return string Path to image
	 */
	abstract public function getPathImage($id, $filename);
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id Photo ID 
	 */
	abstract public function moveLeft($id);

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id Photo ID 
	 */
	abstract public function moveRight($id);

	/**
	 * Returns all photos in gallery. If admin is true 
	 * returns invisible photos too.
	 * 
	 * @param int $id
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getByGallery($id, $admin = false);

	/**
	 * Returns information for photo by given id.
	 * 
	 * @param int $id
	 * @return array|bool
	 */
	abstract public function getById($id);
}
