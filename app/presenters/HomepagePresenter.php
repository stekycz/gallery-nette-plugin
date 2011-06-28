<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class HomepagePresenter extends BasePresenter {

	public function actionDefault() {
		GroupControl::create($this, 'galleries', new GalleryEnvironment(WWW_DIR . '/files/gallery', 'thumbnails'), 'Homepage:gallery');
	}
	
	public function actionGallery($id) {
		ItemControl::create($this, 'photos', new GalleryEnvironment(WWW_DIR . '/files/gallery', 'thumbnails'), $id);
	}
	
	public function actionAdminList() {
		GroupControl::create($this, 'galleries', new GalleryEnvironment(WWW_DIR . '/files/gallery', 'thumbnails'), 'Homepage:adminGallery', 'Homepage:editGallery')
			->setAdmin(true);
	}
	
	public function actionAdminGallery($id) {
		ItemControl::create($this, 'photos', new GalleryEnvironment(WWW_DIR . '/files/gallery', 'thumbnails'), $id)
			->setAdmin(true);
	}
	
	public function renderEditGallery($id) {
	}

}
