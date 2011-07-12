<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class GroupControl extends AbstractGalleryControl {

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
	 * @var string Namespace for groups
	 */
	protected $namespace = null;

	/**
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param GalleryEnvironment $environment
	 * @param string $actionViewItems Action to view all items in group
	 * @param string $actionEditGroup Action to edit group
	 */
	public function __construct(ComponentContainer $parent, $name, GalleryEnvironment $environment, $actionViewItems, $actionEditGroup = null) {
		parent::__construct($parent, $name, $environment);
		$this->actionViewItems = $actionViewItems;
		$this->actionEditGroup = $actionEditGroup;
		$this->templateFile = dirname(__FILE__) . '/groups.latte';
		$this->snippetName = 'group-table';
	}

	/**
	 * Creates new instance.
	 * 
	 * @param ComponentContainer $parent
	 * @param string $name
	 * @param GalleryEnvironment $environment
	 * @param string $actionViewItems Action to view all items in group
	 * @param string $actionEditGroup Action to edit group
	 * @return GroupControl
	 */
	public static function create(ComponentContainer $parent, $name, GalleryEnvironment $environment, $actionViewItems, $actionEditGroup = null) {
		return new self($parent, $name, $environment, $actionViewItems, $actionEditGroup);
	}

	/**
	 * @param bool $admin
	 * @return GroupControl
	 */
	public function setAdmin($admin) {
		if ($admin && !$this->actionEditGroup) {
			throw new LogicException('Action for Group Edit can not be empty if admin mode is enabled.');
		}
		return parent::setAdmin($admin);
	}
	
	/**
	 * Setup namespace for current control.
	 * 
	 * @param string $namsespace 
	 * @return GroupControl Fluent interface
	 */
	public function useNamespace($namespace) {
		if (!in_array($namespace, (array) $this->environment->namespaces)) {
			throw new InvalidArgumentException('Namespace [' . $namespace . '] is not defined in configuration.');
		}
		
		$this->namespace = $namespace;		
		return $this;
	}
	
	public function render() {
		$this->template->actionViewItems = $this->actionViewItems;
		$this->template->actionEditGroup = $this->actionEditGroup;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->namespace = $this->namespace;
		
		$paginator = $this['paginator']->getPaginator();
		
		if ($this->namespace) {
			$this->environment->groupModel->useNamespace($this->namespace);
		}
		
		$this->template->groups = $this->environment->groupModel
			->getAll($paginator->page, $paginator->itemsPerPage, $this->isAdmin);
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	public function handleToggleActive($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->groupModel->toggleActive($id);
		$this->invalidateControl($this->snippetName);
	}

	public function handleDelete($id) {
		$this->template->setFile($this->templateFile);
		$this->environment->groupModel->delete($id);
		$this->invalidateControl($this->snippetName);
	}
	
	public function createComponentPaginator() {
		$vp = new VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = $this->environment->groupsPerPage;
		$paginator->itemCount = $this->environment->groupModel->getCount($this->isAdmin);
		return $vp;
	}

}
