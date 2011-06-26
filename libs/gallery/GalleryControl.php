<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class GalleryControl extends Control {

	/**
	 * @var bool Show admin environment?
	 */
	protected $isAdmin = false;
	/**
	 * @var IGallery
	 */
	protected $model;
	/**
	 * @var string Action which shows gallery
	 */
	protected $actionGallery;
	/**
	 * @var string Action which allows to edit gallery
	 */
	protected $actionEditGallery = null;

	/**
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param IGallery $model
	 * @param string $actionGallery Action to view all items in gallery
	 * @param string $actionEditGallery Action to edit gallery
	 */
	public function __construct(ComponentContainer $parent, $name, IGallery $model, $actionGallery, $actionEditGallery = null) {
		parent::__construct($parent, $name);
		$this->model = $model;
		$this->actionGallery = $actionGallery;
		$this->actionEditGallery = $actionEditGallery;
	}

	/**
	 * Creates new instance.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param IGallery $model
	 * @param string $actionGallery Action to view all items in gallery
	 * @param string $actionEditGallery Action to edit gallery
	 * @return GalleryControl
	 */
	public static function create(ComponentContainer $parent, $name, IGallery $model, $actionGallery, $actionEditGallery = null) {
		return new self($parent, $name, $model, $actionGallery, $actionEditGallery);
	}

	/**
	 * @param bool $admin
	 * @return GalleryControl
	 */
	public function setAdmin($admin) {
		if ($admin && !$this->actionEditGallery) {
			throw new LogicException('Action for Gallery Edit can not be empty if admin mode is enabled.');
		}
		
		$this->isAdmin = $admin;
		return $this;
	}

	/**
	 * Renders gallery list.
	 */
	public function render() {
		$this->template->actionGallery = $this->actionGallery;
		$this->template->actionEditGallery = $this->actionEditGallery;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->galleries = $this->model->getAll($this->isAdmin);
		$this->template->filesBaseUri = $this->model->getBaseUri();
		$this->template->setFile(dirname(__FILE__) . '/galleries.latte');
		$this->template->render();
	}

	/**
	 * Toggles activity/visibility of gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	public function handleToggleActive($id) {
		$this->template->setFile(dirname(__FILE__) . '/galleries.latte');
		$this->model->toggleActive($id);
		$this->invalidateControl('gallery-table');
	}

	/**
	 * Deletes gallery.
	 * 
	 * @param int $id Gallery ID
	 */
	public function handleDelete($id) {
		$this->template->setFile(dirname(__FILE__) . '/galleries.latte');
		$this->model->delete($id);
		$this->invalidateControl('gallery-table');
	}

}
