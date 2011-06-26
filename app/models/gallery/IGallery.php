<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
interface IGallery {
	
	public function create();
	
	public function update();
	
	public function toggleActive($id);
	
	public function delete($id);
	
	public function getAll($admin = false);
	
	public function getBaseUri();
	
}
