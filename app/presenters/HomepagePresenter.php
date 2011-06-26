<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class HomepagePresenter extends BasePresenter {

	public function actionDefault() {
		GalleryControl::create($this, 'galleries', new Gallery(), 'Homepage:gallery');
	}
	
	public function actionGallery($id) {
		PhotoControl::create($this, 'photos', new Photo(), $id);
	}
	
	public function actionAdminList() {
		GalleryControl::create($this, 'galleries', new Gallery(), 'Homepage:adminGallery', 'Homepage:editGallery')
			->setAdmin(true);
	}
	
	public function actionAdminGallery($id) {
		PhotoControl::create($this, 'photos', new Photo(), $id)
			->setAdmin(true);
	}
	
	public function renderEditGallery($id) {
	}

}
