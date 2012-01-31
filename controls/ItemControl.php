<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * 
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011.06.26
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace steky\nette\gallery\controls;
use \Nette\ComponentModel\Container,
	\steky\nette\gallery\IDataProvider,
	\steky\nette\gallery\models\AbstractGroup,
	\steky\nette\gallery\models\AbstractItem,
	\ImageHelper;

/**
 * Contains basic implementation for item control.
 */
class ItemControl extends AbstractGalleryControl {

	/**
	 * @var int ID for group in which are shown items
	 */
	protected $group_id;
	
	/**
	 * @param Nette\ComponentModel\Container $parent
	 * @param string $name
	 * @param ImageHelper $imageHelper
	 * @param steky\nette\gallery\models\AbstractGroup $groupModel
	 * @param steky\nette\gallery\models\AbstractItem $itemModel
	 * @param int $group_id
	 */
	public function __construct(Container $parent, $name, ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel, $group_id) {
		parent::__construct($parent, $name, $imageHelper, $groupModel, $itemModel);
		$this->group_id = $group_id;
		$this->templateFile = __DIR__ . '/items.latte';
		$this->snippetName = 'itemTable';
	}
	
	public function render() {
		$this->template->isAdmin = $this->isAdmin;
		
		$this->template->group = $this->groupModel->getById($this->group_id);
		
		$this->template->items = $this->itemModel->getByGallery($this->group_id, $this->isAdmin);
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}
	
	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->toggleActive($id);
		$this->invalidateControl($this->snippetName);
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->delete($id);
		$this->invalidateControl($this->snippetName);
	}
	
	/**
	 * Changes ordering of file to left.
	 * 
	 * @param int $id
	 */
	public function handleMoveLeft($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->moveLeft($id);
		$this->invalidateControl($this->snippetName);
	}

	/**
	 * Changes ordering of file to right.
	 * 
	 * @param int $id
	 */
	public function handleMoveRight($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->moveRight($id);
		$this->invalidateControl($this->snippetName);
	}
	
}
