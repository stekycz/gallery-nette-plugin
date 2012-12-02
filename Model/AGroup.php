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

namespace stekycz\gallery\Model;

use \stekycz\gallery\AModel;
use \stekycz\gallery\IDataProvider;

/**
 * Defines basic functionality for group model.
 */
abstract class AGroup extends AModel {

	const DEFAULT_NAMESPACE_ID = 1;

	const FORM_FILES_KEY = 'photos';

	/**
	 * @var array Names of columns which are required
	 */
	protected static $basicColumns = array(
		'gallery_id', 'is_active',
	);

	/**
	 * If namespace is not set default root folder is used for saving groups.
	 *
	 * @var string Namespace for groups
	 */
	protected $namespace_id;

	/**
	 * @param \stekycz\gallery\IDataProvider $dataProvider
	 * @param string $basePath
	 */
	public function __construct(IDataProvider $dataProvider, $basePath) {
		parent::__construct($dataProvider, $basePath);
		$this->namespace_id = static::DEFAULT_NAMESPACE_ID;
	}

	/**
	 * Setup namespace for current model. If directory for namespace does not
	 * exists creates it.
	 *
	 * @param int $namespace_id
	 * @return \stekycz\gallery\Model\AGroup Fluent interface
	 */
	public function useNamespace($namsespace_id) {
		$this->namespace_id = $namsespace_id;

		$dir_path = $this->getPathNamespace();
		if (!file_exists($dir_path)) {
			mkdir($dir_path, 0777, true);
		}

		return $this;
	}

	/**
	 * Returns path to namespace folder. If namespace is not used
	 * basePath is returned.
	 *
	 * @return string Path to folder
	 */
	abstract public function getPathNamespace();

	/**
	 * Returns path to gallery folder. Gallery is define by ID.
	 *
	 * @param int $id Gallery ID
	 * @return string Path to folder
	 */
	abstract public function getPathGallery($id);

	/**
	 * Returns count of groups which are not deleted. If admin is true counts
	 * invisible groups too.
	 *
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getCount($admin = false);

	/**
	 * Returns all groups which are not deleted. If admin is true returns
	 * invisible groups too.
	 *
	 * @param bool $admin
	 * @return array
	 */
	abstract public function getAll($page = 1, $itemPerPage = 25, $admin = false);

	/**
	 * Returns information for gallery by given id.
	 *
	 * @param int $id
	 * @return array|bool
	 */
	abstract public function getById($id);

}
