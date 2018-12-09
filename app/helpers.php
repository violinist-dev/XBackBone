<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('storage')) {
	/**
	 * Get a filesystem instance given a path
	 * @param string $root
	 * @return Filesystem
	 */
	function storage($root = null): Filesystem
	{
		global $app;
		$storagePath = $app->getContainer()->get('settings')['storage_dir'];
		return new Filesystem(new Local($root !== null ? $root : $storagePath));
	}
}

if (!function_exists('humanFileSize')) {
	/**
	 * Generate a human readable file size
	 * @param $size
	 * @param int $precision
	 * @return string
	 */
	function humanFileSize($size, $precision = 2): string
	{
		for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {
		}
		return round($size, $precision) . ' ' . ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
	}
}

if (!function_exists('removeDirectory')) {
	/**
	 * Remove a directory and it's content
	 * @param $path
	 */
	function removeDirectory($path)
	{
		$files = glob($path . '/*');
		foreach ($files as $file) {
			is_dir($file) ? removeDirectory($file) : unlink($file);
		}
		rmdir($path);
		return;
	}
}

if (!function_exists('cleanDirectory')) {
	/**
	 * Removes all directory contents
	 * @param $path
	 */
	function cleanDirectory($path)
	{
		$directoryIterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
		$iteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iteratorIterator as $file) {
			if ($file->getFilename() !== '.gitkeep') {
				$file->isDir() ? rmdir($file) : unlink($file);
			}
		}
	}
}

if (!function_exists('redirect')) {
	/**
	 * Set the redirect response
	 * @param \Slim\Http\Response $response
	 * @param string $path
	 * @param array $args
	 * @param null $status
	 * @return \Slim\Http\Response
	 */
	function redirect(\Slim\Http\Response $response, string $path, $args = [], $status = null)
	{
		if ($path === '/' || $path === './' || substr($path, 0, 1) === '/') {
			$url = urlFor($path);
		} else {
			$url = route($path, $args);
		}

		return $response->withRedirect($url, $status);
	}
}

if (!function_exists('urlFor')) {
	/**
	 * Generate the app url given a path
	 * @param string $path
	 * @return string
	 */
	function urlFor(string $path): string
	{
		global $app;
		$baseUrl = $app->getContainer()->get('settings')['base_url'];
		return $baseUrl . $path;
	}
}

if (!function_exists('route')) {
	/**
	 * Generate the app url given a path
	 * @param string $path
	 * @param array $args
	 * @return string
	 */
	function route(string $path, array $args = []): string
	{
		global $app;
		$uri = $app->getContainer()->get('router')->pathFor($path, $args);
		return urlFor($uri);
	}
}

if (!function_exists('lang')) {
	/**
	 * @param string $key
	 * @param array $args
	 * @return string
	 */
	function lang(string $key, $args = []): string
	{
		return \App\Web\Lang::getInstance()->get($key, $args);
	}
}

if (!function_exists('isBot')) {
	/**
	 * @param string $userAgent
	 * @return boolean
	 */
	function isBot(string $userAgent)
	{
		$bots = [
			'TelegramBot',
			'facebookexternalhit/',
			'Discordbot/',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:38.0) Gecko/20100101 Firefox/38.0', // The discord service bot?
			'Facebot',
		];

		foreach ($bots as $bot) {
			if (stripos($userAgent, $bot) !== false) {
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('mime2font')) {
	function mime2font($mime)
	{
		$classes = [
			'image' => 'fa-file-image',
			'audio' => 'fa-file-audio',
			'video' => 'fa-file-video',
			'application/pdf' => 'fa-file-pdf',
			'application/msword' => 'fa-file-word',
			'application/vnd.ms-word' => 'fa-file-word',
			'application/vnd.oasis.opendocument.text' => 'fa-file-word',
			'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word',
			'application/vnd.ms-excel' => 'fa-file-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel',
			'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel',
			'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint',
			'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint',
			'text/plain' => 'fa-file-alt',
			'text/html' => 'fa-file-code',
			'application/json' => 'fa-file-code',
			'application/gzip' => 'fa-file-archive',
			'application/zip' => 'fa-file-archive',
		];

		foreach ($classes as $fullMime => $class) {
			if (strpos($mime, $fullMime) === 0) {
				return $class;
			}
		}
		return 'fa-file';
	}
}