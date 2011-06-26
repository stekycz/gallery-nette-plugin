<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class PhotoControl extends Control {
	
	/**
	 * @var array
	 */
	private $photos = array();
	/**
	 * @var string URI files path
	 */
	private $galleriesBaseUri = '/files';
	/**
	 * @var bool Show admin environment?
	 */
	private $isAdmin = false;
	
	/**
	 * Creates new instance of control.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @return PhotoControl
	 */
	public static function create(ComponentContainer $parent, $name) {
		return new self($parent, $name);
	}
	
	/**
	 * @param array $photos
	 * @return PhotoControl
	 */
	public function setPhotos($photos) {
		$this->photos = $photos;
		return $this;
	}
	
	/**
	 * @param string $baseUri
	 * @return PhotoControl
	 */
	public function setBaseUri($baseUri) {
		$this->galleriesBaseUri = $baseUri;
		return $this;
	}
	
	/**
	 * @param bool $admin
	 * @return PhotoControl
	 */
	public function setAdmin($admin) {
		$this->isAdmin = $admin;
		return $this;
	}
	
	/**
	 * Renders photogallery.
	 */
	public function render() {
		$this->template->photos = $this->photos;
		$this->template->filesBaseUri = $this->galleriesBaseUri;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->template->render();
	}
	
}
