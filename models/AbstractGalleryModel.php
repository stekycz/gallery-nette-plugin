<?php

namespace steky\nette\gallery\models;
use \Nette\Object,
	\steky\nette\gallery\IDataProvider;

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011.06.28
 */
abstract class AbstractGalleryModel extends Object {

	/**
	 * @var IDataProvider
	 */
	protected $dataProvider;
	/**
	 * @var string
	 */
	protected $basePath;

	/**
	 * Creates new instance.
	 *
	 * @param IDataProvider $dataProvider
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
