# Gallery Nette Plugin

## Description

Addon helps to create galleries of photos.

This is build for PHP 5.3.* and based on [Nette Framerwork](https://github.com/nette/nette).

## Dependencies

It depends on [Nette Framerwork](https://github.com/nette/nette), [dibi](https://github.com/dg/dibi),
[MultipleFileUploader](http://addons.nette.org/cs/multiplefileupload) and
[Visual Paginator](http://addons.nette.org/cs/visualpaginator) is also required for paging. If you want to use
your own implementation of paginator you have to change one line in code.

### Notes

* Database layer can be replaced with ```DataProvider```.
* [MultipleFileUploader](http://addons.nette.org/cs/multiplefileupload) is optional but recomanded.

## Installation

1. Copy all files into folder with your project (libs dir).

2. Install all database tables into your database. If it is needed change ```DataProvider```.
   Default database structure is in ```/DataProvider/mysql.sql```
   If you want to add custom columns just edit existing tables.

3. For easier usage create services for model layer.
    Example:
```yaml
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

4. Use plugin and create controls:
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

## Notes

In example templates is used fancybox javascript library for photogalleries and
fugue icons. Templates should be changed/replaced for using in your own project.

## Author

The author of the addon is [Martin Štekl](mailto:martin.stekl@gmail.com).
