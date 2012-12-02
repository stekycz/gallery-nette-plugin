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

use \stekycz\gallery\AControl;
use \stekycz\gallery\Model\AGroup;
use \stekycz\gallery\Model\AItem;
use \ImageHelper;

class ItemControl extends AControl {

	/**
	 * @var array Item data
	 */
	protected $item;

	public function __construct(ImageHelper $imageHelper, AGroup $groupModel, AItem $itemModel, $item, $isAdmin = false) {
		parent::__construct($imageHelper, $groupModel, $itemModel, $isAdmin);
		$this->item = $item;
		$this->templateFile = __DIR__ . '/templates/item.latte';
	}

	public function render() {
		$this->template->isAdmin = $this->isAdmin;
		$this->template->item = $this->item;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	/**
	 * @param int $id
	 */
	public function handleToggleActive($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->itemModel->toggleActive($id);
			$this->invalidateControl($this->snippetName);
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param int $id
	 */
	public function handleDelete($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->itemModel->delete($id);
			$this->invalidateControl($this->snippetName);
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * Changes ordering of file to left.
	 *
	 * @param int $id
	 */
	public function handleMoveLeft($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->itemModel->moveLeft($id);
			$this->invalidateControl($this->snippetName);
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * Changes ordering of file to right.
	 *
	 * @param int $id
	 */
	public function handleMoveRight($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->itemModel->moveRight($id);
			$this->invalidateControl($this->snippetName);
		} else {
			$this->redirect('this');
		}
	}

}
