<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;

$root = JPATH_SITE;
$path = '';
//$current = 'images/sampledata/parks/animals';
$current = $this->state->get('filter.activepath');
$subs = [];
$dirs = explode('/', $current);
$params = ComponentHelper::getParams('com_mediacat');

foreach ($dirs as $dir)
{
	if (empty($dir))
	{
		continue;
	}
	// skip if dir begins with .
	if (strpos($dir, '.') === 0)
	{
		continue;
	}
	$path .= '/' . $dir;
	foreach (new DirectoryIterator($root . $path) as $fileInfo)
	{
		if($fileInfo->isDot())
		{
			continue;
		}
		// skip if dir begins with .
		if (strpos($fileInfo->getFilename(), '.') === 0)
		{
			continue;
		}
		if ($fileInfo->isDir())
		{
			$subs[] = $path . '/' . $fileInfo->getFilename();
		}
	}
}

asort($subs);

/*
 * /files/odt
 * /files/pdf
 * /files/png
 * /files/webp
 */

// so push the file path onto the fron of the list
array_unshift($subs, '/' . $params->get('file_path'));

foreach ($subs as $sub)
{
	// make an array
	$members = explode('/', substr($sub, 1));
	$space = count($members) -1;
	$active = ($sub == $current) ? ' active' : '';
	echo '<div class="cat-folder indent-' . $space . $active . '" data-link="'. $sub .'">';
	if (!$active)
	{
		echo '<a href="#" onclick="setFolder(\''.$sub.'\');return false;">';
		echo '<span class="icon-folder"></span> ';
	}
	else
	{
		echo '<span class="icon-folder-open"></span> ';
	}
	echo array_pop($members);
	if (!$active)
	{
		echo '</a>';
	}
	echo '</div>' . "\n";
}
