<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class ItemControl extends AbstractGalleryControl {

	/**
	 * @var int
	 */
	protected $group_id;
	
	/**
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param GalleryEnvironment $environment
	 * @param int $group_id
	 */
	public function __construct(ComponentContainer $parent, $name, GalleryEnvironment $environment, $group_id) {
		parent::__construct($parent, $name, $environment);
		$this->group_id = $group_id;
		$this->templateFile = dirname(__FILE__) . '/items.latte';
		$this->snippetName = 'item-table';
	}
	
	/**
	 * Creates new instance of control.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param GalleryEnvironment $environment
	 * @param int $group_id
	 * @return ItemControl
	 */
	public static function create(ComponentContainer $parent, $name, GalleryEnvironment $environment, $group_id) {
		return new self($parent, $name, $environment, $group_id);
	}
	
	public function render() {
		$this->template->isAdmin = $this->isAdmin;
		
		$gallery = $this->environment->groupModel->getById($this->group_id);
		$this->template->namespace = $gallery['namespace'];
		
		$this->template->items = $this->environment->itemModel->getByGallery($this->group_id, $this->isAdmin);
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}
	
	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->itemModel->toggleActive($id);
		$this->invalidateControl($this->snippetName);
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->itemModel->delete($id);
		$this->invalidateControl($this->snippetName);
	}
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id
	 */
	public function handleMoveLeft($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->itemModel->moveLeft($id);
		$this->invalidateControl($this->snippetName);
	}

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id
	 */
	public function handleMoveRight($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->itemModel->moveRight($id);
		$this->invalidateControl($this->snippetName);
	}
	
}
