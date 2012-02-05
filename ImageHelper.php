<?php

use \Nette\Object,
	\Nette\Caching\Cache,
	\Nette\Image,
	\Nette\Utils\Strings,
	\Nette\Utils\Html,
	\Nette\InvalidStateException;

/**
 * Image helper with automatic image resize and cache.
 *
 * @author Roman Ozana, ozana@omdesign.cz
 * @link www.omdesign.cz
 *
 * Updated for new Nette 2.0 and for PHP 5.3+ by Martin Štekl
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @link www.steky.cz
 * @license MIT
 *
 * add to presenter before render function
 * $this->template->registerHelper('resize', callback($this->context->imageHelper, 'resize'));
 * $this->template->registerHelper('gallery', callback($this->context->imageHelper, 'gallery'));
 *
 * in template
 * {= 'media/image.jpg'|resize:40}
 * {= 'media/image.jpg'|resize:?x20}
 * {= 'media/image.jpg'|resize:40:'alt'}
 * {= 'media/image.jpg'|resize:40:'alt':'title'}
 *
 * gallery link
 * {= 'media/image.jpg'|gallery}
 */
class ImageHelper extends Object {

	/**
	 * @var Nette\Caching\Cache
	 */
	protected $cache;

	/**
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * @var string
	 */
	protected $tempDir;

	/**
	 * @var timestamp Default value is 1 day
	 */
	protected $cacheExpireTimestamp;

	/**
	 * @param string $baseUrl Absolute web URL
	 * @param Nette\Caching\Cache $cache
	 */
	public function __construct(Cache $cache, $baseUrl, $tempDir) {
		$this->cache = $cache->derive('images');
		$this->baseUrl = $baseUrl;
		$this->tempDir = $tempDir;
		$this->cacheExpireTimestamp = 24*60*60; // default value is 1 day
	}

	/**
	 * Setup timestamp for cache expiration for each image.
	 *
	 * @param timestamp|int $timestamp
	 */
	public function setCacheExpireTimestamp($timestamp) {
		$this->cacheExpireTimestamp = $timestamp;
	}

	/**
	 * Helper for simple image tag with resize
	 *
	 * @param string $filename
	 * @param int $width
	 * @param int $height
	 */
	public function resize($filename, $dimensions = '120x90', $alt = null, $title = null) {
		$info = pathinfo($filename);

		$title = !is_null($alt) && is_null($title) ? $alt : $title;
		$alt = is_null($alt) ? basename($filename, '.' . $info['extension']) : $alt;

		list($src, $width, $height) = $this->resizeImageWithCache($filename, $dimensions);

		return Html::el('img')->src($this->baseUrl . $src)->width($width)->height($height)->alt($alt)->title($title)->class('thumbnail');
	}

	/**
	 * Render gallery image with anchor to bigger sized image
	 * @param string $filename
	 * @param string $dimensions
	 * @param string $alt
	 * @param string $title
	 * @param string $big
	 */
	public function gallery($filename, $alt = null, $title = null, $rel = null, $dimensions = '120x90', $full_dimensions = '640x480') {
		$img = $this->resize($filename, $dimensions, $alt, $title);
		list($src, $width, $height) = $this->resizeImageWithCache($filename, $full_dimensions);
		return Html::el('a')->href($this->baseUrl . $src)->title($title)->rel($rel)->class('fancybox')->add($img);
	}

	/**
	 * Resize image and save thumbs to cache
	 * @param string $original
	 * @param string $dimensions
	 * @param string $subfolder
	 * @param string $public_root
	 * @return array
	 */
	public function resizeImageWithCache($original, $dimensions, $public_root = WWW_DIR) {
		$original_absolute_path = $public_root . '/' . $original;
		if (!is_file($original_absolute_path)) {
			return array($original, null, null); // check if file exist
		}

		// check internal cache for image
		$subfolder = $this->tempDir;
		$subfolder_absolute_path = $public_root . '/' . $subfolder;
		$cache = $this->cache;
		$key = md5($original . $dimensions . $subfolder . $public_root);

		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$info = pathinfo($original_absolute_path);

		// read dimensions
		$dim = explode('x', $dimensions, null);
		$newWidth = isset($dim[0]) ? $dim[0] : null;
		$newHeight = isset($dim[1]) ? $dim[1] : null;

		// check public cache directory
		if (!is_dir($subfolder_absolute_path) || !is_writable($subfolder_absolute_path)) {
			throw new InvalidStateException('Thumbnail path ' . $subfolder_absolute_path . ' does not exists or is not writable.');
		}

		try {
			$cache_path = $subfolder_absolute_path . '/' . md5(dirname($original));
			if (!is_dir($cache_path)) {
				mkdir($cache_path);
			}

			$image = Image::fromFile($original_absolute_path);

			$image->resize((int) $newWidth, (int) $newHeight);
			$cache_file = Strings::webalize(basename($original, '.' . $info['extension']) . '-' . $image->getWidth() . 'x' . $image->getHeight()) . '.' . $info['extension'];
			$image->save($cache_path . '/' . $cache_file);

			// resize image name
			$resize = $subfolder . '/' . md5(dirname($original)) . '/' . $cache_file;
			$result = array($resize, $image->getWidth(), $image->getHeight());

			// save result to internal cache
			$cache->save($key, $result, array(
				Cache::FILES => $original,
				Cache::EXPIRATION => time() + $this->cacheExpireTimestamp,
			));

			return $result;
		} catch (Exception $e) {
			return array($original, null, null);
		}
	}

}
