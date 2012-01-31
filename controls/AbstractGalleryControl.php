<?php
/**
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * 
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011.06.28
 * @license MIT
 * @copyright Copyright (c) 2011, 2012 Martin Štekl <martin.stekl@gmail.com>
 */

namespace steky\nette\gallery\controls;
use \Nette\Application\UI\Control,
	\Nette\ComponentModel\Container,
	\steky\nette\gallery\IDataProvider,
	\steky\nette\gallery\models\AbstractGroup,
	\steky\nette\gallery\models\AbstractItem,
	\ImageHelper;

/**
 * Defines basic functionality for controls.
 */
abstract class AbstractGalleryControl extends Control {

	/**
	 * @var bool Show admin environment?
	 */
	protected $isAdmin = false;
	/**
	 * @var string Path to file with component template
	 */
	protected $templateFile;
	/**
	 * @var string Name of snippet in template
	 */
	protected $snippetName;
	/**
	 * @var ImageHelper Helps with work around pictures
	 */
	protected $imageHelper;
	
	/**
	 * @var steky\nette\gallery\models\AbstractGroup
	 */
	protected $groupModel;
	/**
	 * @var steky\nette\gallery\models\AbstractItem
	 */
	protected $itemModel;

	/**
	 * @param Nette\ComponentModel\Container $parent
	 * @param string $name
	 * @paramm ImageHelper $imageHelper
	 * @param steky\nette\gallery\models\AbstractGroup $groupModel
	 * @param steky\nette\gallery\models\AbstractItem $itemModel
	 */
	public function __construct(Container $parent, $name, ImageHelper $imageHelper, AbstractGroup $groupModel, AbstractItem $itemModel) {
		parent::__construct($parent, $name);
		$this->imageHelper = $imageHelper;
		$this->groupModel = $groupModel;
		$this->itemModel = $itemModel;
	}

	/**
	 * @param bool $admin
	 * @return AbstractGalleryControl
	 */
	public function setAdmin($admin) {
		$this->isAdmin = $admin;
		return $this;
	}

	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$template->registerHelper('resize', callback($this->imageHelper, 'resize'));
		$template->registerHelper('gallery', callback($this->imageHelper, 'gallery'));
		return $template;
	}

	/**
	 * Setups template file and snippet name if is filled.
	 *
	 * @param string $templateFile
	 * @param string $snippetName
	 */
	public function setupTemplate($templateFile, $snippetName = null) {
		$this->templateFile = $templateFile;
		if ($snippetName !== null) {
			$this->snippetName = $snippetName;
		}
	}

	/**
	 * Renders control.
	 */
	abstract public function render();

	/**
	 * Toggles activity/visibility.
	 *
	 * @param int $id
	 */
	abstract public function handleToggleActive($id);

	/**
	 * @param int $id
	 */
	abstract public function handleDelete($id);

}
