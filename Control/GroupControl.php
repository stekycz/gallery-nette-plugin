<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2012.02.05
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace stekycz\gallery\Control;

use \Nette\InvalidArgumentException;
use \stekycz\gallery\AControl;
use \stekycz\gallery\Model\AGroup;
use \stekycz\gallery\Model\AItem;
use \ImageHelper;

class GroupControl extends AControl {

	/**
	 * @var string Action which shows item list
	 */
	protected $actionViewItems;

	/**
	 * @var string Action which allows to edit group
	 */
	protected $actionEditGroup = null;

	/**
	 * @var array Group data
	 */
	protected $group;

	/**
	 * @param \ImageHelper $imageHelper
	 * @param \stekycz\gallery\Model\AGroup $groupModel
	 * @param \stekycz\gallery\Model\AItem $itemModel
	 * @param string $actionViewItems Action to view all items in group
	 * @param array $group Group data
	 * @param bool $isAdmin
	 * @param string|null $actionEditGroup Action to edit group
	 * @throws \Nette\InvalidArgumentException
	 */
	public function __construct(ImageHelper $imageHelper, AGroup $groupModel, AItem $itemModel, $actionViewItems, $group, $isAdmin = false, $actionEditGroup = null) {
		parent::__construct($imageHelper, $groupModel, $itemModel, $isAdmin);
		$this->actionViewItems = $actionViewItems;
		$this->group = $group;
		if ($this->isAdmin && $actionEditGroup === null) {
			throw new InvalidArgumentException('GroupControl can not be set as Admin without correct edit action.');
		}
		$this->actionEditGroup = $actionEditGroup;
		$this->templateFile = __DIR__ . '/templates/group.latte';
	}

	public function render() {
		$this->template->actionViewItems = $this->actionViewItems;
		$this->template->actionEditGroup = $this->actionEditGroup;
		$this->template->isAdmin = $this->isAdmin;

		$this->template->group = $this->group;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->groupModel->toggleActive($id);
		$this->invalidateControl();
		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->groupModel->delete($id);
		$this->invalidateControl();
		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}
	}

}
