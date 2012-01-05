<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
class GalleryEnvironment extends DiContainer {

	/**
	 * @var string Key for photos in array from which is created/updated group
	 */
	protected $formFilesKey = 'photos';

	/**
	 * @var string Key for photo in array from which is created/updated item
	 */
	protected $fileKey = 'file';

	/**
	 * @var string Path to full files
	 */
	protected $basePath;

	/**
	 * @var DibiConnection
	 */
	private $database;

	/**
	 * Creates new instance of gallery environment.
	 * 
	 * @param DibiConnection $database Instance of database connection
	 */
	public function __construct(DibiConnection $database) {
		// Default basePath must be here
		$this->basePath = WWW_DIR . '/gallery';
		$this->database = $database;
	}

	/**
	 * Makes basic setup of gallery service.
	 * 
	 * @param array $params Parameters in associative array
	 */
	public function setup(array $params = array()) {
		foreach ($this->getReflection()->getProperties() as $property) {
			$property_name = $property->getName();
			if (array_key_exists($property_name, $params)) {
				$this->__set($property_name, $params[$property_name]);
			}
		}
		$this->createModelServices($params);
	}

	/**
	 * Creates model services from given configuration or default.
	 * 
	 * @param array $params Associative array for sub-services
	 */
	protected function createModelServices(array $params = array()) {
		$default_service_class = array(
			'itemModel' => 'Photo',
			'groupModel' => 'Group',
		);
		$params += $default_service_class;

		// Add Item Model service
		$this->addService('itemModel', new $params['itemModel']($this, $this->database));

		// Add Group Model service
		$this->addService('groupModel', new $params['groupModel']($this, $this->database));
	}

	public function __set($name, $value) {
		if ($this->getReflection()->hasProperty($name)) {
			return $this->{$name} = $value;
		}
		return parent::__set($name, $value);
	}

	public function &__get($name) {
		if ($this->getReflection()->hasProperty($name)) {
			return $this->{$name};
		} else if ($name == 'namespaces') {
			$return = $this->getNamespaces();
			return $return;
		}
		return parent::__get($name);
	}

	protected function getNamespaces() {
		static $cache = null;

		if ($cache === null) {
			$cache = $this->database->fetchPairs('
				SELECT tgn.namespace_id, tgn.name
				FROM gallery_namespace AS tgn
			');
		}

		return $cache;
	}

}
