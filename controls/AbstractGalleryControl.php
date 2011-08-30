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
	 * @var string Path to file with component template
	 */
	protected $templateFile;
	/**
	 * @var string Name of snippet in template
	 */
	protected $snippetName;
	
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
	
	protected function createTemplate() {
		$template = parent::createTemplate();
		$template->registerHelper('resize', 'ImageHelper::resize');
		$template->registerHelper('gallery', 'ImageHelper::gallery');
		return $template;
	}

	/**
	 * Setups template file and snippet name if is filled.
	 * 
	 * @param string $templateFile
	 * @param string $snippetName
	 */
	public function setupTemplate($templateFile, $snippetName = null) {
		$this->templateFile = $templateFile;
		if ($snippetName !== null) {
			$this->snippetName = $snippetName;
		}
	}

	/**
	 * Renders control.
	 */
	abstract public function render();
	
	/**
	 * Toggles activity/visibility.
	 * 
	 * @param int $id
	 */
	abstract public function handleToggleActive($id);
	
	/**
	 * @param int $id
	 */
	abstract public function handleDelete($id);
	
}
