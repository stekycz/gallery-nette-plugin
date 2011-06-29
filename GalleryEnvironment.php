<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
class GalleryEnvironment extends Object {

	/**
	 * @var int Quality of resized images
	 */
	protected $imageQuality = 100;
	/**
	 * @var int Maximum size of image in one direction
	 */
	protected $imageSize = 640;
	/**
	 * @var int Maximum width of thumbnail image
	 */
	protected $thumbnailWidth = 120;
	/**
	 * @var int Maximum height of thumbnail image
	 */
	protected $thumbnailHeight = 90;
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
	
	public function getBasePath() {
		return $this->basePath;
	}
	
	public function getFileKey() {
		return $this->fileKey;
	}
	
	public function getFormFilesKey() {
		return $this->formFilesKey;
	}
	
	public function getImageQuality() {
		return $this->imageQuality;
	}
	
	public function getImageSize() {
		return $this->imageSize;
	}
	
	public function getThumbnailHeight() {
		return $this->thumbnailHeight;
	}
	
	public function getThumbnailWidth() {
		return $this->thumbnailWidth;
	}
	
	public function getThumbnailsDirName() {
		return $this->thumbnailsDirName;
	}
	
}
