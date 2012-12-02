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

use \Nette\Object;

/**
 * Defines basic functionality for model.
 */
abstract class AModel extends Object {

	/**
	 * @var \stekycz\gallery\IDataProvider
	 */
	protected $dataProvider;

	/**
	 * @var string
	 */
	protected $basePath;

	/**
	 * Creates new instance.
	 *
	 * @param \stekycz\gallery\IDataProvider $dataProvider
	 * @param string $basePath
	 */
	public function __construct(IDataProvider $dataProvider, $basePath) {
		$this->dataProvider = $dataProvider;
		$this->basePath = $basePath;
	}

	/**
	 * Creates new group/item from given data.
	 *
	 * @param array $data
	 * @return int $id
	 */
	abstract public function create(array $data);

	/**
	 * Updates data.
	 *
	 * @param array $data
	 * @return int $id
	 */
	abstract public function update(array $data);

	/**
	 * Toggles activity/visibility of group/item.
	 *
	 * @param int $id
	 */
	abstract public function toggleActive($id);

	/**
	 * Deletes given group/item.
	 *
	 * @param int $id
	 */
	abstract public function delete($id);

}
