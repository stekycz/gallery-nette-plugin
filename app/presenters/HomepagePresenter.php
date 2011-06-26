<?php

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2011-06-26
 */
class HomepagePresenter extends BasePresenter {

	public function renderDefault() {
		GalleryControl::create($this, 'galleries')
			->setGalleries(Gallery::getAll())
			->setBaseUri($this->getHttpRequest()->url->baseUrl . '/files/gallery')
			->setGalleryAction('Homepage:gallery');
	}
	
	public function renderGallery($id) {
		PhotoControl::create($this, 'photos')
			->setPhotos(Photo::getByGallery($id))
			->setBaseUri($this->getHttpRequest()->url->baseUrl . '/files/gallery');
	}
	
	public function renderAdminList() {
		GalleryControl::create($this, 'galleries')
			->setGalleries(Gallery::getAll(true))
			->setAdmin(true)
			->setBaseUri($this->getHttpRequest()->url->baseUrl . '/files/gallery')
			->setGalleryAction('Homepage:adminGallery');
	}
	
	public function renderAdminGallery($id) {
		PhotoControl::create($this, 'photos')
			->setPhotos(Photo::getByGallery($id, true))
			->setAdmin(true)
			->setBaseUri($this->getHttpRequest()->url->baseUrl . '/files/gallery');
	}
	
	public function renderEditGallery($id) {
	}

}
