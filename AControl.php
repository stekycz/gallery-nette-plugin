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

namespace stekycz\gallery;

use \Nette\Application\UI\Control;
use \Nette\ComponentModel\Container;
use \stekycz\gallery\Model\AGroup;
use \stekycz\gallery\Model\AItem;
use \ImageHelper;

/**
 * Defines basic functionality for controls.
 */
abstract class AControl extends Control {

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
	 * @var \ImageHelper Helps with work around pictures
	 */
	protected $imageHelper;

	/**
	 * @var \stekycz\gallery\Model\AGroup
	 */
	protected $groupModel;

	/**
	 * @var \stekycz\gallery\Model\AItem
	 */
	protected $itemModel;

	/**
	 * @paramm ImageHelper $imageHelper
	 * @param \stekycz\gallery\Model\AGroup $groupModel
	 * @param \stekycz\gallery\Model\AItem $itemModel
	 * @param bool $isAdmin
	 */
	public function __construct(ImageHelper $imageHelper, AGroup $groupModel, AItem $itemModel, $isAdmin = false) {
		$this->imageHelper = $imageHelper;
		$this->groupModel = $groupModel;
		$this->itemModel = $itemModel;
		$this->isAdmin = $isAdmin;
	}

	protected function createTemplate($class = null) {
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

}
