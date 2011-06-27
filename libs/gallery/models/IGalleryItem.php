<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
interface IGalleryItem {
	
	public function create();
	
	public function update();
	
	public function toggleActive($id);
	
	public function delete($id);
	
	public function moveLeft($id);
	
	public function moveRight($id);
	
	public function getByGallery($id, $admin = false);
	
	public function getBaseUri();
	
}
