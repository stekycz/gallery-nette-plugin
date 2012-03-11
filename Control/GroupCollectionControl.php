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

namespace steky\nette\gallery\Control;
use \Nette\InvalidArgumentException,
	\steky\nette\gallery\AbstractControl,
	\steky\nette\gallery\IDataProvider,
	\steky\nette\gallery\Model\AbstractGroup,
	\steky\nette\gallery\Model\AbstractItem,
	\ImageHelper,
	\VisualPaginator;

/**
 * Contains basic implementation for group control.
 */
class GroupCollectionControl extends AbstractControl {

	const DEFAULT_ITEMS_PER_PAGE = 25;

	/**
	 * @param ImageHelper $imageHelper
	 * @param steky\nette\gallery\models\AbstractGroup $groupModel
	 * @param steky\nette\gallery\models\AbstractItem $itemModel
	 * @param array $namespaces Exists namespaces in associative array
	 */
	public function __construct(ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel) {
		parent::__construct($imageHelper, $groupModel, $itemModel);

		$this->templateFile = __DIR__ . '/groups.latte';
		$this->useNamespace(AbstractGroup::DEFAULT_NAMESPACE_ID);
	}

	/**
	 * Setup namespace for current control.
	 *
	 * @param string $namsespace_id
	 * @return steky\nette\gallery\controls\GroupControl Fluent interface
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

		$groups = $this->groupModel->getAll($paginator->page, $paginator->itemsPerPage, $this->isAdmin);
		foreach ($groups as $group) {
			$control = new GroupControl($this->imageHelper, $this->groupModel, $this->itemModel, $this->isAdmin ? 'Homepage:adminGallery' : 'Homepage:gallery', $group);
			$control->setAdmin($this->isAdmin, 'Homepage:editGallery');
			$this->addComponent($control, 'group_'.sha1(serialize($group)));
		}

		$this->template->render();
	}

	public function createComponentPaginator() {
		$vp = new VisualPaginator();
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = static::DEFAULT_ITEMS_PER_PAGE;
		$paginator->itemCount = $this->groupModel->getCount($this->isAdmin);
		return $vp;
	}

}
