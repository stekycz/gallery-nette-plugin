<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractItem extends AbstractGalleryModel {
	
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
	
	abstract public function moveLeft($id);
	
	abstract public function moveRight($id);
	
	abstract public function getByGallery($id, $admin = false);
	
}
