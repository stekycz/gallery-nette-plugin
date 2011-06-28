<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
class GalleryEnvironment extends Object {

	/**
	 * @var string Path to full files
	 */
	protected $basePath;
	/**
	 * @var string Path to thumbnails
	 */
	protected $thumbnailsDirName;

	/**
	 * Creates new instance of gallery environment. Given paths must be absolute.
	 * 
	 * @param string $basePath Path to full files
	 * @param string $thumbnailsDirName Thumbnails directory name
	 */
	function __construct($basePath, $thumbnailsDirName) {
		$this->basePath = $basePath;
		$this->thumbnailsDirName = $thumbnailsDirName;
	}
	
	public function getBasePath() {
		return $this->basePath;
	}

	public function getThumbnailsDirName() {
		return $this->thumbnailsDirName;
	}

		
	/**
	 * Returns model for items;
	 * 
	 * @return AbstractItem
	 */
	public function getItemModel() {
		return Photo::getInstance($this);
	}
	
	/**
	 * Returns model for groups.
	 * 
	 * @return AbstractGroup
	 */
	public function getGroupModel() {
		return Group::getInstance($this);
	}

}
