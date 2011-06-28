<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
class GalleryEnvironment extends Object {

	/**
	 * @var string Uri to full files
	 */
	protected $baseUri;
	/**
	 * @var string Path to full files
	 */
	protected $basePath;
	/**
	 * @var string Uri to thumbnails
	 */
	protected $thumbnailsUri;
	/**
	 * @var string Path to thumbnails
	 */
	protected $thumbnailsPath;

	/**
	 * Creates new instance of gallery environment. Given uri/path must be 
	 * relative to WWW_DIR/$baseUri.
	 * 
	 * @param string $baseUri Uri to full files
	 * @param string $basePath Path to full files
	 * @param string $thumbnailsUri Uri to thumbnails
	 * @param string $thumbnailsPath Path to thumbnails
	 */
	function __construct($baseUri, $thumbnailsUri, $basePath = null, $thumbnailsPath = null) {
		$this->baseUri = $baseUri;
		$this->thumbnailsUri = $thumbnailsUri;
		$this->basePath = $basePath ?: $baseUri;
		$this->thumbnailsPath = $thumbnailsPath ?: $thumbnailsUri;
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
	 * Returns model for galleries.
	 * 
	 * @return AbstractGroup
	 */
	public function getGroupModel() {
		return Group::getInstance($this);
	}

}
