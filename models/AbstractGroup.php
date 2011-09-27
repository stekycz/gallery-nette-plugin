<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractGroup extends AbstractGalleryModel {

	const DEFAULT_NAMESPACE_ID = 1;
	
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
	 * If namespace is not set default root folder is used for saving groups.
	 * 
	 * @var string Namespace for groups
	 */
	protected $namespace_id = self::DEFAULT_NAMESPACE_ID;

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
	 * Setup namespace for current model. If directory for namespace does not
	 * exists creates it.
	 * 
	 * @param int $namespace_id
	 * @return AbstractGroup Fluent interface
	 */
	public function useNamespace($namsespace_id) {
		$this->namespace_id = $namsespace_id;
		
		$dir_path = $this->getPathNamespace();
		if (!file_exists($dir_path)) {
			mkdir($dir_path, 0777, true);
		}
		
		return $this;
	}
	
	/**
	 * Returns path to namespace folder. If namespace is not used 
	 * basePath is returned.
	 * 
	 * @return string Path to folder
	 */
	abstract public function getPathNamespace();
	
	/**
	 * Returns path to gallery folder. Gallery is define by ID.
	 * 
	 * @param int $id Gallery ID
	 * @return string Path to folder
	 */
	abstract public function getPathGallery($id);

	/**
	 * Returns count of groups which are not deleted. If admin is true counts 
	 * invisible groups too.
	 * 
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getCount($admin = false);
	
	/**
	 * Returns all groups which are not deleted. If admin is true returns 
	 * invisible groups too.
	 * 
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getAll($page = 1, $itemPerPage = 25, $admin = false);

	/**
	 * Returns information for gallery by given id.
	 * 
	 * @param int $id
	 * @return array|bool
	 */
	abstract public function getById($id);
}
