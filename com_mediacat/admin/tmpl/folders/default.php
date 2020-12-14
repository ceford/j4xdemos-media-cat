<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

$params = ComponentHelper::getParams('com_mediacat');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
//	->useStyle('com_mediacat.mediamanager')
	->useScript('com_mediacat.mediacat');

$path = realpath(JPATH_SITE . '/images');
$root = JPATH_SITE;
$rootlen = strlen($root);

$folders[] = '/images';

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
		RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object) {
	if (!is_dir($name))
	{
		continue;
	}
	$fileName = $object->getFilename();
	if ($fileName == '.' || $fileName == '..')
	{
		continue;
	}
	$folders[] = substr($name, $rootlen);
}
sort($folders);

var_dump($folders);