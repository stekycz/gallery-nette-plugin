<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2012.03.27
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace stekycz\gallery\Control;

use \stekycz\gallery\AbstractControl;
use \stekycz\gallery\Model\AbstractGroup;
use \stekycz\gallery\Model\AbstractItem;
use \ImageHelper;

class ItemControl extends AbstractControl {

	/**
	 * @var array Item data
	 */
	protected $item;

	public function __construct(ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel, $item) {
		parent::__construct($imageHelper, $groupModel, $itemModel);
		$this->item = $item;
		$this->templateFile = __DIR__ . '/item.latte';
	}

	public function render() {
		$this->template->isAdmin = $this->isAdmin;
		$this->template->item = $this->item;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->toggleActive($id);
		$this->invalidateControl();
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->delete($id);
		$this->invalidateControl();
	}

	/**
	 * Changes ordering of file to left.
	 *
	 * @param int $id
	 */
	public function handleMoveLeft($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->moveLeft($id);
		$this->invalidateControl();
	}

	/**
	 * Changes ordering of file to right.
	 *
	 * @param int $id
	 */
	public function handleMoveRight($id) {
		$this->template->setFile($this->templateFile);
		$this->itemModel->moveRight($id);
		$this->invalidateControl();
	}

}
