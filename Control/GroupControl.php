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
use \stekycz\gallery\AbstractControl;
use \stekycz\gallery\Model\AbstractGroup;
use \stekycz\gallery\Model\AbstractItem;
use \ImageHelper;

class GroupControl extends AbstractControl {

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
	 * @param \stekycz\gallery\Model\AbstractGroup $groupModel
	 * @param \stekycz\gallery\Model\AbstractItem $itemModel
	 * @param string $actionViewItems Action to view all items in group
	 *  @param array $group Group data
	 */
	public function __construct(ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel, $actionViewItems, $group) {
		parent::__construct($imageHelper, $groupModel, $itemModel);
		$this->actionViewItems = $actionViewItems;
		$this->group = $group;

		$this->templateFile = __DIR__ . '/group.latte';
	}

	/**
	 * @param bool $admin
	 * @param string $actionEditGroup Action to edit group
	 * @return \stekycz\gallery\Control\GroupControl
	 */
	public function setAdmin($admin, $actionEditGroup = null) {
		if ($actionEditGroup === null) {
			throw new InvalidArgumentException('GroupControl can not be set as Admin without correct edit action.');
		}
		$this->actionEditGroup = $actionEditGroup;
		return parent::setAdmin($admin);
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
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->groupModel->delete($id);
		$this->invalidateControl();
	}

}
