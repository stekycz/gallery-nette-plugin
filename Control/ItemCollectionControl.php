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

use \stekycz\gallery\AbstractControl;
use \stekycz\gallery\Model\AbstractGroup;
use \stekycz\gallery\Model\AbstractItem;
use \ImageHelper;

/**
 * Contains basic implementation for item control.
 */
class ItemCollectionControl extends AbstractControl {

	/**
	 * @var int ID for group in which are shown items
	 */
	protected $group_id;

	/**
	 * @param ImageHelper $imageHelper
	 * @param \stekycz\gallery\Model\AbstractGroup $groupModel
	 * @param \stekycz\gallery\Model\AbstractItem $itemModel
	 * @param int $group_id
	 */
	public function __construct(ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel, $group_id) {
		parent::__construct($imageHelper, $groupModel, $itemModel);
		$this->group_id = $group_id;
		$this->templateFile = __DIR__ . '/items.latte';
	}

	public function render() {
		$this->template->setFile($this->templateFile);

		$this->template->group = $this->groupModel->getById($this->group_id);

		$items = $this->itemModel->getByGallery($this->group_id, $this->isAdmin);
		foreach ($items as $item) {
			$control = new ItemControl($this->imageHelper, $this->groupModel, $this->itemModel, $item);
			$control->setAdmin($this->isAdmin);
			$this->addComponent($control, 'item_'.sha1(serialize($item)));
		}

		$this->template->render();
	}

}
