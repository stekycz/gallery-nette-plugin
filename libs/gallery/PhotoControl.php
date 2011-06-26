<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class PhotoControl extends Control {

	/**
	 * @var bool Show admin environment?
	 */
	protected $isAdmin = false;
	/**
	 * @var IGalleryItem
	 */
	protected $model;
	/**
	 * @var int Gallery ID
	 */
	protected $gallery_id;
	
	public function __construct(ComponentContainer $parent, $name, IGalleryItem $model, $gallery_id) {
		parent::__construct($parent, $name);
		$this->model = $model;
		$this->gallery_id = $gallery_id;
	}
	
	/**
	 * Creates new instance of control.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @return PhotoControl
	 */
	public static function create(ComponentContainer $parent, $name, IGalleryItem $model, $gallery_id) {
		return new self($parent, $name, $model, $gallery_id);
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
		$this->template->isAdmin = $this->isAdmin;
		$this->template->photos = $this->model->getByGallery($this->gallery_id, $this->isAdmin);
		$this->template->filesBaseUri = $this->model->getBaseUri();
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->template->render();
	}
	
	/**
	 * Toggles activity/visibility of photo.
	 * 
	 * @param int $id Photo ID
	 */
	public function handleToggleActive($id) {
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->model->toggleActive($id);
		$this->invalidateControl('photo-table');
	}

	/**
	 * Deletes photo.
	 * 
	 * @param int $id Photo ID
	 */
	public function handleDelete($id) {
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->model->delete($id);
		$this->invalidateControl('photo-table');
	}
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id Photo ID
	 */
	public function handleMoveLeft($id) {
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->model->moveLeft($id);
		$this->invalidateControl('photo-table');
	}

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id Photo ID
	 */
	public function handleMoveRight($id) {
		$this->template->setFile(dirname(__FILE__) . '/photos.latte');
		$this->model->moveRight($id);
		$this->invalidateControl('photo-table');
	}
	
}
