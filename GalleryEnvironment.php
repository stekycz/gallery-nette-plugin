<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
class GalleryEnvironment extends DiContainer {

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
	 * @var string Name for directory with thumbnails in each gallery
	 */
	protected $thumbnailsDirName = 'thumbnails';

	/**
	 * Creates new instance of gallery environment. Given paths must be absolute.
	 * 
	 * @param string $basePath Path to full files
	 */
	public function __construct() {
		// Must be here
		$this->basePath = WWW_DIR . '/gallery';
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
