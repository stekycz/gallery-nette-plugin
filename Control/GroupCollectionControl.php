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
use \VisualPaginator;

/**
 * Contains basic implementation for group control.
 */
class GroupCollectionControl extends AControl {

	const DEFAULT_ITEMS_PER_PAGE = 25;

	/**
	 * @var array
	 */
	private $groups;

	/**
	 * @param \ImageHelper $imageHelper
	 * @param \stekycz\gallery\Model\AGroup $groupModel
	 * @param \stekycz\gallery\Model\AItem $itemModel
	 * @param bool $isAdmin
	 */
	public function __construct(ImageHelper $imageHelper, AGroup $groupModel, AItem $itemModel, $isAdmin = false) {
		parent::__construct($imageHelper, $groupModel, $itemModel, $isAdmin);
		$this->templateFile = __DIR__ . '/templates/groups.latte';
		$this->useNamespace(AGroup::DEFAULT_NAMESPACE_ID);
	}

	/**
	 * Setup namespace for current control.
	 *
	 * @param string $namsespace_id
	 * @return \stekycz\gallery\Control\GroupCollectionControl Fluent interface
	 */
	public function useNamespace($namespace_id) {
		if ($namespace_id) {
			$this->groupModel->useNamespace($namespace_id);
		}
		return $this;
	}

	public function render($groups_per_page = null) {
		$this->template->setFile($this->templateFile);

		$paginator = $this['paginator']->getPaginator();
		if ($groups_per_page) {
			$paginator->itemsPerPage = $groups_per_page;
		}

		$this->groups = $this->groupModel->getAll($paginator->page, $paginator->itemsPerPage, $this->isAdmin);
		$this->template->group_ids = array_keys($this->groups);

		$this->template->render();
	}

	public function createComponentGroup() {
		$imageHelper = $this->imageHelper;
		$groupModel = $this->groupModel;
		$itemModel = $this->itemModel;
		$isAdmin = $this->isAdmin;
		$groups = $this->groups;
		return new Multiplier(function ($group_id) use ($imageHelper, $groupModel, $itemModel, $isAdmin, $groups) {
			$control = new GroupControl(
				$imageHelper,
				$groupModel,
				$itemModel,
				$isAdmin ? 'Homepage:adminGallery' : 'Homepage:gallery',
				$groups[$group_id],
				$isAdmin, 'Homepage:editGallery'
			);
			return $control;
		});
	}

	public function createComponentPaginator() {
		$vp = new VisualPaginator();
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = static::DEFAULT_ITEMS_PER_PAGE;
		$paginator->itemCount = $this->groupModel->getCount($this->isAdmin);
		return $vp;
	}

}
