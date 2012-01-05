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
	 * @var DibiConnection
	 */
	protected $database;
	
	/**
	 * Creates new instance.
	 * 
	 * @param GalleryEnvironment $environment 
	 */
	public function __construct(GalleryEnvironment $environment, DibiConnection $database) {
		$this->environment = $environment;
		$this->database = $database;
	}
	
	/**
	 * Creates new group/item from given data.
	 * 
	 * @param array $data
	 * @return int $id
	 */
	abstract public function create(array $data);
	
	/**
	 * Updates data.
	 * 
	 * @param array $data
	 * @return int $id
	 */
	abstract public function update(array $data);
	
	/**
	 * Toggles activity/visibility of group/item.
	 * 
	 * @param int $id
	 */
	abstract public function toggleActive($id);
	
	/**
	 * Deletes given group/item.
	 * 
	 * @param int $id
	 */
	abstract public function delete($id);
	
}
