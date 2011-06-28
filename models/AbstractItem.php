<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractItem extends AbstractGalleryModel {
	
	abstract public function moveLeft($id);
	
	abstract public function moveRight($id);
	
	abstract public function getByGallery($id, $admin = false);
	
}
