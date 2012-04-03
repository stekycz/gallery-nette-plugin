# Gallery Nette Plugin

## Description

Plugin helps to create galleries of photos in a lot of ways.

It is build for PHP 5.3+ and based on Nette Framerwork 2.0.

## Dependencies

It depends on [Nette Framework](http://nette.org/), [dibi](http://dibiphp.com/) and
[MultipleFileUploader](http://addons.nette.org/cs/multiplefileupload).
[Visual Paginator](http://addons.nette.org/cs/visualpaginator) is required only
for paging. If you want to use your own implementation of paginator you have to
change one line in code.

### Notes

* Database layer can be replaced with DataProvider.
* MultipleFileUploader is optional but recomanded.

## Installation

1. Copy all files into folder with your project (libs dir).

2. Install all database tables into your database. If it is needed change DataProvider.
   Default database structure is in /DataProvider/mysql.sql
   If you want to add custom columns just edit existing tables.

3. For easier usage create services for model layer.
```neon
	parameters:
		imageHelper:
			baseUrl: http://example.com/
			tempDir: files/temp
		gallery:
			basePath: %wwwDir%/files/gallery
	services:
		imageHelper:
			class: \ImageHelper(@cache, %imageHelper.baseUrl%,  %imageHelper.tempDir%)
		galleryDataProvider:
			class: \steky\nette\gallery\DataProvider\Dibi(@database)
		galleryItemModel:
			class: \steky\nette\gallery\models\Item(@galleryDataProvider, %gallery.basePath%)
		galleryGroupModel:
			class: \steky\nette\gallery\models\Group(@galleryDataProvider, %gallery.basePath%)
```
4. Use plugin and create controls.
```php
	new GroupControl($this, 'galleries',
		$this->context->imageHelper,
		$this->context->galleryGroupModel,
		$this->context->galleryItemModel,
		$this->context->galleryDataProvider->namespaces,
		'Homepage:gallery'
	);
```
```php
	new ItemControl($this, 'photos',
		$this->context->imageHelper,
		$this->context->galleryGroupModel,
		$this->context->galleryItemModel,
		$id
	);
```
## Demo

Demo application can be found on [nette-gallery.steky.cz](nette-gallery.steky.cz).

In example templates is used [fancybox](http://fancybox.net/) javascript library
for photogalleries and [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
CSS library. Templates should be changed/replaced for using in your own project.

## Author

The author of the toolkit is [Martin Štekl](mailto:martin.stekl@gmail.com).
