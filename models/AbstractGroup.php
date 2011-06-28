<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
abstract class AbstractGroup extends AbstractGalleryModel {
	
	abstract public function getAll($admin = false);
	
}
