<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
abstract class AbstractGalleryModel extends Object {
	
	/**
	 * @var GalleryEnvironment
	 */
	protected $environment;
	/**
	 * @var IGallery
	 */
	private static $instance = null;
	
	/**
	 * Creates new instance.
	 * 
	 * @param GalleryEnvironment $environment 
	 */
	private function __construct(GalleryEnvironment $environment) {
		$this->environment = $environment;
	}
	
	/**
	 * Returns instance.
	 * 
	 * @param GalleryEnvironment $environment
	 * @return AbstractGalleryModel
	 */
	public static function getInstance(GalleryEnvironment $environment) {
		if (self::$instance === null) {
			$called_class = get_called_class();
			self::$instance = new $called_class($environment);
		}
		return self::$instance;
	}
	
	abstract public function create(array $data);
	
	abstract public function update(array $data);
	
	abstract public function toggleActive($id);
	
	abstract public function delete($id);
	
}
