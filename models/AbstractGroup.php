<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractGroup extends AbstractGalleryModel {
	
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
	
	abstract public function getAll($admin = false);
	
}
