<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-28
 */
abstract class AbstractGalleryControl extends Control {
	
	/**
	 * @var bool Show admin environment?
	 */
	protected $isAdmin = false;
	/**
	 * @var GalleryEnvironment
	 */
	protected $environment;
	
	/**
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param GalleryEnvironment $environment
	 */
	public function __construct(ComponentContainer $parent, $name, GalleryEnvironment $environment) {
		parent::__construct($parent, $name);
		$this->environment = $environment;
	}
	
	/**
	 * @param bool $admin
	 * @return AbstractGalleryControl
	 */
	public function setAdmin($admin) {
		$this->isAdmin = $admin;
		return $this;
	}
	
}
