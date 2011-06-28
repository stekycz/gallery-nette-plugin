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
	 * Renders group list.
	 */
	public function render() {
		$this->template->actionViewItems = $this->actionViewItems;
		$this->template->actionEditGroup = $this->actionEditGroup;
		$this->template->isAdmin = $this->isAdmin;
		$this->template->groups = $this->environment->groupModel->getAll($this->isAdmin);
		$this->template->setFile(dirname(__FILE__) . '/groups.latte');
		$this->template->render();
	}

	/**
	 * Toggles activity/visibility of group.
	 * 
	 * @param int $id Gallery ID
	 */
	public function handleToggleActive($id) {
		$this->template->setFile(dirname(__FILE__) . '/groups.latte');
		$this->environment->groupModel->toggleActive($id);
		$this->invalidateControl('group-table');
	}

	/**
	 * Deletes group.
	 * 
	 * @param int $id Gallery ID
	 */
	public function handleDelete($id) {
		$this->template->setFile(dirname(__FILE__) . '/groups.latte');
		$this->environment->groupModel->delete($id);
		$this->invalidateControl('group-table');
	}

}
