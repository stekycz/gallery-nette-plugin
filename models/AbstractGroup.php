<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractGroup extends AbstractGalleryModel {

	/**
	 * @var array Names of columns which are required
	 */
	protected static $basicColumns = array(
		'gallery_id', 'is_active',
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
	 * Returns all groups which are not deleted. If admin is true returns 
	 * invisible groups too.
	 * 
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getAll($admin = false);

	/**
	 * Returns information for gallery by given id.
	 * 
	 * @param int $id
	 * @return array|bool
	 */
	abstract public function getById($id);
}
