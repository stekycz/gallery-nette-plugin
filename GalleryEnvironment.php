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
	 * @var int Groups per page
	 */
	protected $groupsPerPage = 25;
		
	/**
	 * @var string Path to full files
	 */
	protected $basePath;
	/**
	 * @var array List of namespaces
	 */
	protected $namespaces = array();

	/**
	 * Creates new instance of gallery environment.
	 * 
	 * @param array $params Parameters for service
	 */
	public function __construct($params) {
		// Must be here
		$this->basePath = WWW_DIR . '/gallery';
		
		if (array_key_exists('gallery', $params)) {
			$this->configure($params['gallery']);
			$this->createModelServices($params['gallery']);
		}
	}
	
	/**
	 * Creates new instance from given context.
	 * 
	 * This should be used for addService only!
	 * 
	 * @param DiContainer $args Application context
	 * @return GalleryEnvironment
	 */
	public static function getInstance(DiContainer $args) {
		return new self($args->params);
	}
	
	/**
	 * Configure environment from given parameters.
	 * 
	 * @param array $params
	 */
	protected function configure($params) {
		foreach ($this->getReflection()->getProperties() as $property) {
			$name = $property->getName();
			if (array_key_exists($name, $params)) {
				$this->{$name} = $params[$name];
			}
		}
	}
	
	/**
	 * Creates model services from given configuration or default.
	 * 
	 * @param array $params
	 */
	protected function createModelServices($params) {
		// Add Item Model service
		if (array_key_exists('itemModel', $params)) {
			$this->addService('itemModel', $params['itemModel']::getInstance($this));
		} else {
			$this->addService('itemModel', Photo::getInstance($this));
		}
		
		// Add Group Model service
		if (array_key_exists('groupModel', $params)) {
			$this->addService('groupModel', $params['groupModel']::getInstance($this));
		} else {
			$this->addService('groupModel', Group::getInstance($this));
		}
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
		}
		return parent::__get($name);
	}

}
