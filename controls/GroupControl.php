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
	\Nette\InvalidArgumentException,
	\steky\nette\gallery\IDataProvider,
	\steky\nette\gallery\models\AbstractGroup,
	\steky\nette\gallery\models\AbstractItem,
	\ImageHelper,
	\VisualPaginator;

/**
 * Contains basic implementation for group control.
 */
class GroupControl extends AbstractGalleryControl {

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
	 * @param Nette\ComponentModel\Container $parent
	 * @param string $name
	 * @param ImageHelper $imageHelper
	 * @param steky\nette\gallery\models\AbstractGroup $groupModel
	 * @param steky\nette\gallery\models\AbstractItem $itemModel
	 * @param array $namespaces Exists namespaces in associative array
	 * @param string $actionViewItems Action to view all items in group
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
	 * @param string $actionEditGroup Action to edit group
	 * @return steky\nette\gallery\controls\GroupControl
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
	 * @param string $namsespace_id
	 * @return steky\nette\gallery\controls\GroupControl Fluent interface
	 */
	public function useNamespace($namespace_id) {
		if (!in_array($namespace_id, array_keys($this->namespaces))) {
			throw new InvalidArgumentException('Namespace [' . $namespace_id . '] does not exist.');
		}

		$this->namespace_id = $namespace_id;
		return $this;
	}

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

		$this->template->groups = $groupModel
				->getAll($paginator->page, $paginator->itemsPerPage, $this->isAdmin);
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->groupModel->toggleActive($id);
		$this->invalidateControl($this->snippetName);
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->groupModel->delete($id);
		$this->invalidateControl($this->snippetName);
	}

	public function createComponentPaginator($name) {
		$vp = new VisualPaginator($this, $name);
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = static::DEFAULT_ITEMS_PER_PAGE;
		$paginator->itemCount = $this->groupModel->getCount($this->isAdmin);
		return $vp;
	}

}
