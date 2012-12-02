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

namespace stekycz\gallery\Control;

use \stekycz\gallery\AControl;
use Nette\Application\UI\Multiplier;
use \stekycz\gallery\Model\AGroup;
use \stekycz\gallery\Model\AItem;
use \ImageHelper;

/**
 * Contains basic implementation for item control.
 */
class ItemCollectionControl extends AControl {

	/**
	 * @var int ID for group in which are shown items
	 */
	protected $group_id;

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @param \ImageHelper $imageHelper
	 * @param \stekycz\gallery\Model\AGroup $groupModel
	 * @param \stekycz\gallery\Model\AItem $itemModel
	 * @param int $group_id
	 * @param bool $isAdmin
	 */
	public function __construct(ImageHelper $imageHelper, AGroup $groupModel, AItem $itemModel, $group_id, $isAdmin = false) {
		parent::__construct($imageHelper, $groupModel, $itemModel, $isAdmin);
		$this->group_id = $group_id;
		$this->items = $this->itemModel->getByGallery($this->group_id, $this->isAdmin);
		$this->templateFile = __DIR__ . '/templates/items.latte';
	}

	public function render() {
		$this->template->setFile($this->templateFile);
		$this->template->group = $this->groupModel->getById($this->group_id);
		$this->template->item_ids = array_keys($this->items);
		$this->template->render();
	}

	public function createComponentItem() {
		$imageHelper = $this->imageHelper;
		$groupModel = $this->groupModel;
		$itemModel = $this->itemModel;
		$isAdmin = $this->isAdmin;
		$items = $this->items;
		return new Multiplier(function ($item_id) use ($imageHelper, $groupModel, $itemModel, $isAdmin, $items) {
			$control = new ItemControl($imageHelper, $groupModel, $itemModel, $items[$item_id], $isAdmin);
			return $control;
		});
	}

}
