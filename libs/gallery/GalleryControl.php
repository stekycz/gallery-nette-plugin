<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class GalleryControl extends Control {
	
	/**
	 * @var array
	 */
	private $galleriesInfo = array();
	/**
	 * @var string URI files path
	 */
	private $galleriesBaseUri = '/files';
	/**
	 * @var string Action which shows gallery
	 */
	private $action;
	/**
	 * @var bool Show admin environment?
	 */
	private $isAdmin = false;
	
	/**
	 * Creates new instance.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @return GalleryControl
	 */
	public static function create(ComponentContainer $parent, $name) {
		return new self($parent, $name);
	}
	
	/**
	 * @param array $galleries
	 * @return GalleryControl
	 */
	public function setGalleries($galleries) {
		$this->galleriesInfo = $galleries;
		return $this;
	}
	
	/**
	 * @param string $baseUri
	 * @return GalleryControl
	 */
	public function setBaseUri($baseUri) {
		$this->galleriesBaseUri = $baseUri;
		return $this;
	}
	
	/**
	 * @param string $action
	 * @return GalleryControl
	 */
	public function setGalleryAction($action) {
		$this->action = $action;
		return $this;
	}
	
	/**
	 * @param bool $admin
	 * @return GalleryControl
	 */
	public function setAdmin($admin) {
		$this->isAdmin = $admin;
		return $this;
	}
	
	/**
	 * Renders gallery list.
	 */
	public function render() {
		$this->template->action = $this->action;
		$this->template->galleries = $this->galleriesInfo;
		$this->template->filesBaseUri = $this->galleriesBaseUri;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->setFile(dirname(__FILE__) . '/galleries.latte');
		$this->template->render();
	}
	
}
