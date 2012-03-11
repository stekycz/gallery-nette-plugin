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
use \Nette\ComponentModel\Container,
	\Nette\InvalidArgumentException,
	\steky\nette\gallery\AbstractControl,
	\steky\nette\gallery\IDataProvider,
	\steky\nette\gallery\Model\AbstractGroup,
	\steky\nette\gallery\Model\AbstractItem,
	\ImageHelper,
	\VisualPaginator;

/**
 * Contains basic implementation for group control.
 */
class GroupControl extends AbstractControl {

	const DEFAULT_ITEMS_PER_PAGE = 25;

	/**
	 * @var string Action which shows item list
	 */
	protected $actionViewItems;

	/**
	 * @var string Action which allows to edit group
	 */
	protected $actionEditGroup = null;

	/**
	 * If namespace is not set default root folder is used.
	 *
	 * @var int Namespace for groups
	 */
	protected $namespace_id;

	/**
	 * @var array
	 */
	private $namespaces;

	/**
	 * @param \Nette\ComponentModel\Container $parent
	 * @param string $name
	 * @param \ImageHelper $imageHelper
	 * @param \steky\nette\gallery\Model\AbstractGroup $groupModel
	 * @param \steky\nette\gallery\Model\AbstractItem $itemModel
	 * @param string[] $namespaces
	 * @param string $actionViewItems
	 */
	public function __construct(Container $parent, $name, ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel, array $namespaces, $actionViewItems) {
		parent::__construct($parent, $name, $imageHelper, $groupModel, $itemModel);
		$this->actionViewItems = $actionViewItems;
		$this->namespaces = $namespaces;

		$this->templateFile = __DIR__ . '/groups.latte';
		$this->snippetName = 'groupTable';
		$this->namespace_id = AbstractGroup::DEFAULT_NAMESPACE_ID;
	}

	/**
	 * @param bool $admin
	 * @param string|null $actionEditGroup Action to edit group
	 * @return \steky\nette\gallery\AbstractControl
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setAdmin($admin, $actionEditGroup = null) {
		if ($actionEditGroup === null) {
			throw new InvalidArgumentException('GroupControl can not be set as Admin without correct edit action.');
		}
		$this->actionEditGroup = $actionEditGroup;
		return parent::setAdmin($admin);
	}

	/**
	 * Setup namespace for current control.
	 *
	 * @param int $namespace_id
	 * @return \steky\nette\gallery\controls\GroupControl Fluent interface
	 * @throws \Nette\InvalidArgumentException
	 */
	public function useNamespace($namespace_id) {
		if (!in_array($namespace_id, array_keys($this->namespaces))) {
			throw new InvalidArgumentException('Namespace [' . $namespace_id . '] does not exist.');
		}

		$this->namespace_id = $namespace_id;
		return $this;
	}

	/**
	 * @param int $groups_per_page
	 */
	public function render($groups_per_page = self::DEFAULT_ITEMS_PER_PAGE) {
		$this->template->actionViewItems = $this->actionViewItems;
		$this->template->actionEditGroup = $this->actionEditGroup;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->namespace = $this->namespaces[$this->namespace_id];

		$paginator = $this['paginator']->getPaginator();
		$paginator->itemsPerPage = $groups_per_page;

		$groupModel = $this->groupModel;

		if ($this->namespace_id) {
			$groupModel->useNamespace($this->namespace_id);
		}

		$this->template->groups = $groupModel->getAll($paginator->page, $paginator->itemsPerPage, $this->isAdmin);
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	/**
	 * @param int $id
	 */
	public function handleToggleActive($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->groupModel->toggleActive($id);
			$this->invalidateControl($this->snippetName);
		}
	}

	/**
	 * @param int $id
	 */
	public function handleDelete($id) {
		if ($this->presenter->isAjax()) {
			$this->template->setFile($this->templateFile);
			$this->groupModel->delete($id);
			$this->invalidateControl($this->snippetName);
		}
	}

	/**
	 * @param string $name
	 * @return \VisualPaginator
	 */
	public function createComponentPaginator($name) {
		$vp = new VisualPaginator($this, $name);
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = static::DEFAULT_ITEMS_PER_PAGE;
		$paginator->itemCount = $this->groupModel->getCount($this->isAdmin);
		return $vp;
	}

}
