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
	 * Creates new instance.
	 * 
	 * @param GalleryEnvironment $environment 
	 */
	protected function __construct(GalleryEnvironment $environment) {
		$this->environment = $environment;
	}
	
	abstract public function create(array $data);
	
	abstract public function update(array $data);
	
	abstract public function toggleActive($id);
	
	abstract public function delete($id);
	
}
